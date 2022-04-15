<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use App\Exception\User\UserAlreadyExistException;
use App\Messenger\Message\UserRegisteredMessage;
use App\Messenger\RoutingKey;
use App\Repository\UserRepository;
use App\Service\Password\EncoderService;
use App\Service\Request\RequestService;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class UserRegisterService {

    private UserRepository $userRepository;
    private EncoderService $encoderService;
    private MessageBusInterface $messageBusInterface;
    public function __construct(UserRepository $userRepository, EncoderService $encoderService, MessageBusInterface $messageBusInterface)
    {       
        $this->userRepository = $userRepository;
        $this->encoderService = $encoderService;
        $this->messageBusInterface = $messageBusInterface;
        
    }
    
    public function create(Request $request){
        $name = RequestService::getField($request,'name');
        $email = RequestService::getField($request,'email');
        $password = RequestService::getField($request,'password');

        $user = new User($name,$email);
        $user->setPassword($this->encoderService->generateHashedPassword($user,$password));

        try{
            $this->userRepository->save($user);
        }catch (Exception $exception){
            throw UserAlreadyExistException::fromEmail($email);
        }

        $this->messageBusInterface->dispatch(
            new UserRegisteredMessage($user->getId(),$user->getName(),$user->getEmail(),$user->getToken()),
            [new AmqpStamp(RoutingKey::USER_QUEUE)]
        );
        
        return $user;

    
    
    }

}