<?php
declare(strict_types=1);
namespace App\Api\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener{

    public function onJWTCreated(JWTCreatedEvent $event){

        /** @var User $user */
        $user = $event->getUser();
        $payload = $event->getData();
        $payload['id'] = $user->getId();
        unset($payload['roles']);

        $event->setData($payload);
    }
}