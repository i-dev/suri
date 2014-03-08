<?php

namespace Idev\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityManager;

class UserProvider implements UserProviderInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
    	
    	try {
	        $user = $this->em->createQuery("SELECT u FROM Idev\Entity\User u WHERE u.login = ?1")
	        	->setParameter(1, $username)
	        	->getSingleResult();
    	} catch ( \Exception $e ) {
    		throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    	}
    	
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof \Idev\Entity\User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === '\Idev\Entity\User';
    }
}