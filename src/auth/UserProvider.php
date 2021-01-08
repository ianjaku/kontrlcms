<?php

namespace invacto\SimpleCMS\auth;


use invacto\SimpleCMS\repos\UserRepo;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class UserProvider implements UserProviderInterface {

    /**
    * Loads the user for the given username.
    *
    * This method must throw UsernameNotFoundException if the user is not
    * found.
    *
    * @return UserInterface
    *
    * @throws UsernameNotFoundException if the user is not found
    */
    public function loadUserByUsername(string $username)
    {
		$userData = UserRepo::oneByEmail($username);
        if ($userData == null) {
            throw new UsernameNotFoundException();
        }

        return new User(
            $userData->email,
            $userData->password,
            $userData->salt
        );
    }

    /**
    * Refreshes the user.
    *
    * It is up to the implementation to decide if the user data should be
    * totally reloaded (e.g. from the database), or if the UserInterface
    * object can just be merged into some internal array of users / identity
    * map.
    *
    * @return UserInterface
    *
    * @throws UnsupportedUserException  if the user is not supported
    * @throws UsernameNotFoundException if the user is not found
    */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
    * Whether this provider supports the given user class.
    *
    * @return bool
    */
    public function supportsClass(string $class)
    {
        // TODO: actually check if it is the right class
        return true;
    }
}
