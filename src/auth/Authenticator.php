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
//ini_set( 'session.cookie_secure', 1 );
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
        $salt = bin2hex(random_bytes(32));

        $encoderFactory = $this->createEncoderFactory();

        $user = new User($username, "", $salt);
        $encoder = $encoderFactory->getEncoder($user);
        $passwordHash = $encoder->encodePassword($password, $salt);
        $user->setPassword($passwordHash);

//        $params = [
//            ":email" => $user->getUsername(),
//            ":password" => $user->getPassword(),
//            "salt" => $salt
//        ];
        $this->db->table("users")->insert([
        	"email" => $user->getUsername(),
			"password" => $user->getPassword(),
			"salt" => $salt
		]);
//        $this->db->insert("INSERT INTO users (email, password, salt) VALUES (:email, :password, :salt)", $params);
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
//            $this->startSessionForUser($user->getUsername(), "ADMIN");

            return true;
        } catch (AuthenticationException $e) {
            return false;
        }
    }

    private function startSessionForUser($username, $role) {
    	session_start();
    	$_SESSION["username"] = $username;
    	$_SESSION["role"] = $role;
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
        $_SESSION = [];
        setcookie( session_name(), "", time()-3600, "/" );
        session_destroy();
        session_write_close();
    }

    public function loginWithToken($token) {
//    	$result = $this->db->select("SELECT * FROM login_tokens WHERE token = :token", [":token" => $token]);
    	$result = $this->db->table("login_tokesn")->where("token", $token)->get();
    	if (sizeof($result) <= 0) return false;
    	$loginToken = $result[0];

//    	$users = $this->db->select("SELECT * FROM users WHERE id = :id", ["id" => $loginToken["user_id"]]);
		$users = $this->db->table("users")->where("id", $loginToken["user_id"])->get();
    	if (sizeof($users) <= 0) return false;
    	$user = $users[0];

    	$this->startSessionForUser($user["email"], "ADMIN");
    	return true;
	}

    public function createLoginTokenFor($userId) {
		$token = bin2hex(random_bytes(32));
//		$params = [
//			":token" => $token,
//			":user_id" => $userId
//		];
//    	$this->db->insert("INSERT INTO login_tokens (user_id, token) VALUES (:user_id, :token)", $params);
		$this->db->table("login_tokens")->insert([
			"token" => $token,
			"user_id" => $userId
		]);
		return $token;
	}

	private function createEncoderFactory() {
        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $encoders = [
            User::class => $defaultEncoder
        ];
        return new EncoderFactory($encoders);
    }

}

