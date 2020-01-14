<?php

namespace AppBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class Logger
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->logger->info('Utilisateur mis à jour : '.$entity->getId().'  '.$entity->getFirstName().' '.$entity->getLastName());
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->logger->alert('Utilisateur supprimé : '.$entity->getId().'  '.$entity->getFirstName().' '.$entity->getLastName());
    }
}