<?php

declare(strict_types=1);
namespace App\Security\Core\User;
use App\Entity\User;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByUsername(string $username): UserInterface 
    {
        try{
            return $this->userRepository->findOneByEmailOrFail($username);
        }catch(UserNotFoundException $e){
            throw new UserNotFoundException(sprintf('User %s not found', $username));
            
        }
    }

    public function refreshUser(UserInterface $user)
    {
            if(!$user instanceof User){
                throw new UnsupportedException(sprintf('Istances of % are not supported',get_class(($user))));
            }

            return $this->loadUserByUsername($user->getUsername());
    }

    public function upgradePassword($user, $newHashedPassword)
    {
        $user->setPassword($newHashedPassword);
        $this->userRepository->save($user);
    }
    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}   