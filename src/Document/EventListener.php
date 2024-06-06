<?php

// src/EventListener/JWTCreatedListener.php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    /**
     * @param JWTCreatedEvent $event
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        // Récupérer l'utilisateur pour ajouter des données personnalisées au token
        $user = $event->getUser();
        
        if (!$user instanceof UserInterface) {
            return;
        }

        // Ajouter les informations personnalisées au payload
        $payload = $event->getData();
        $payload['firstName'] = $user->getFirstName();
        $payload['lastName'] = $user->getLastName();
        $payload['userName'] = $user->getuserName();
        $payload['birthDate'] = $user->getBirthDate();
        $payload['gender'] = $user->getGender();

        $event->setData($payload);
    }
}