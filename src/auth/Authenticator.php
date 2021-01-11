<?php

namespace invacto\KontrlCMS\auth;

use invacto\KontrlCMS\repos\Database;
use invacto\KontrlCMS\repos\LoginTokenRepo;
use invacto\KontrlCMS\repos\UserRepo;
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
//ini_set( 'session.cookie_secure', 1 );
ini_set( 'session.cookie_samesite', "Strict" );

class Authenticator {

    /**
     * @var string
     */
    private $secretKey;

    private $currentUser = null;


    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function register($username, $password) {
        $salt = bin2hex(random_bytes(32));

        $encoderFactory = $this->createEncoderFactory();

        $user = new User($username, "", $salt);
        $encoder = $encoderFactory->getEncoder($user);
        $passwordHash = $encoder->encodePassword($password, $salt);
        $user->setPassword($passwordHash);

        UserRepo::create($user->getUsername(), $user->getPassword(), $salt);
    }

    public function login($username, $password) {
        $unauthenticatedToken = new UsernamePasswordToken(
            $username,
            $password,
            $this->secretKey
        );

        $userProvider = new UserProvider();
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

            $this->startSession();

			$_SESSION["username"] = $user->getUsername();
            $_SESSION["role"] = "ADMIN";

            return true;
        } catch (AuthenticationException $e) {
            return false;
        }
    }

    private function startSessionForUser($username, $role) {
    	$this->startSession();
    	$_SESSION["username"] = $username;
    	$_SESSION["role"] = $role;
	}

    public function getCurrentUser() {
        if ($this->currentUser !== null) return $this->currentUser;

        if (!isset($_COOKIE["PHPSESSID"])) return false;

        $this->startSession();

        if ($_SESSION["role"] !== "ADMIN") return false;

        $username = $_SESSION["username"];
        if ($username == null) return false;

        return $username;
    }

    public function hasUser() {
        return $this->getCurrentUser() !== false;
    }

    public function logout() {
        $_SESSION = [];
        setcookie( session_name(), "", time()-3600, "/" );
        session_destroy();
        session_write_close();
    }

    public function loginWithToken($token) {
		$loginToken = LoginTokenRepo::oneByToken($token);
    	if ($loginToken == null) return false;

		$user = UserRepo::oneById($loginToken["user_id"]);
    	if ($user == null) return false;

    	$this->startSessionForUser($user["email"], "ADMIN");
    	return true;
	}

    public function createLoginTokenFor($userId) {
		$token = bin2hex(random_bytes(32));
		LoginTokenRepo::create($token, $userId);
		return $token;
	}

	private function createEncoderFactory() {
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder
        ];
        return new EncoderFactory($encoders);
    }

    private function startSession() {
    	if (!isset($_SESSION)) {
    		session_start();
		}
	}

}

