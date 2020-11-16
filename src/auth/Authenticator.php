<?php

namespace invacto\SimpleCMS;

use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

class Authenticator {

    /**
     * @var string
     */
    private $secretKey;


    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    function login($username, $password) {
        $unauthenticatedToken = new UsernamePasswordToken(
            $username,
            $password,
            $this->secretKey
        );

//        $authenticatedToken =
    }

}
