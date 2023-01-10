<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserSubscriber implements EventSubscriberInterface
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function cryptPassword(ViewEvent $event)
    {

        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($entity instanceof User && in_array($method, [Request::METHOD_POST, Request::METHOD_PUT])) {
            $entity->setPassword($this->passwordHasher->hashPassword(
                $entity,
                $entity->getPassword()
            ));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['cryptPassword', EventPriorities::PRE_WRITE],
        ];
    }
}
