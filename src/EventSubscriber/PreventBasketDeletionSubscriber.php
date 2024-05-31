<?php

namespace App\EventSubscriber;

use App\Entity\Basket;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Doctrine\ORM\Exception\ORMException;

class PreventBasketDeletionSubscriber implements EventSubscriber
{
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Basket && $entity->getIdBasketProduct() !== null) {
            $this->flashBag->add('error', 'Cannot delete basket because it has a product associated.');

            // Cancel deletion by throwing an exception
            throw new ORMException('Cannot delete basket because it has a product associated.');
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            'preRemove',
        ];
    }
}