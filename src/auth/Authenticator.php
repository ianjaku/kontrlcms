<?php

namespace invacto\SimpleCMS\auth;

use invacto\SimpleCMS\Database;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

// Set safer Cookie standards
ini_set( 'session.cookie_httponly', 1 );
ini_set( 'session.cookie_secure', 1 );
ini_set( 'session.cookie_samesite', "Strict" );

class Authenticator {

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var Database
     */
    private $db;

    private $currentUser = null;


    public function __construct(string $secretKey, Database $db)
    {
        $this->secretKey = $secretKey;
        $this->db = $db;
    }

    public function register($username, $password) {
        $salt = "test";


        $encoderFactory = $this->createEncoderFactory();

        $user = new User($username, "", $salt);
        $encoder = $encoderFactory->getEncoder($user);
        $passwordHash = $encoder->encodePassword($password, $salt);
        $user->setPassword($passwordHash);

        $params = [
            ":email" => $user->getUsername(),
            ":password" => $user->getPassword(),
            "salt" => $salt
        ];
        $this->db->insert("INSERT INTO users (email, password, salt) VALUES (:email, :password, :salt)", $params);
    }

    public function login($username, $password) {
        $unauthenticatedToken = new UsernamePasswordToken(
            $username,
            $password,
            $this->secretKey
        );

        $userProvider = new UserProvider($this->db);
        $userChecker = new UserChecker();

        $encoderFactory = $this->createEncoderFactory();

        $daoProvider = new DaoAuthenticationProvider(
            $userProvider,
            $userChecker,
            $this->secretKey,
            $encoderFactory
        );

        try {
            $user = $daoProvider->authenticate($unauthenticatedToken);

            session_start();

            $_SESSION["username"] = $user->getUsername();
            $_SESSION["role"] = "ADMIN";

            return true;
        } catch (AuthenticationException $e) {
            return false;
        }
    }

    public function getCurrentUser() {
        if ($this->currentUser !== null) return $this->currentUser;

        if (!isset($_COOKIE["PHPSESSID"])) return false;

        session_start();

        if ($_SESSION["role"] !== "ADMIN") return false;

        $username = $_SESSION["username"];
        if ($username == null) return false;

        return $username;
    }

    public function hasUser() {
        return $this->getCurrentUser() !== false;
    }

    public function logout() {
        setcookie (session_id(), "", time() - 3600);
        session_destroy();
        session_write_close();
    }

    private function createEncoderFactory() {
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder
        ];
        return new EncoderFactory($encoders);
    }

}

