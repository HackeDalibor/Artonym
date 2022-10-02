<?php

namespace App\EventListener;
use App\Entity\Notification;
use Doctrine\ORM\Event\OnFlushEventArgs;

class Notifier
{
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $om = $eventArgs->getObjectManager();
        foreach($om->getUnitOfWork()->getScheduledEntityInsertions() as $object)
        {
            if(get_class($object) === 'App\Entity\Subject')
            {
                if(null !== $object->getUser()->getFollowers())
                {
                    foreach($object->getUser()->getFollowers() as $follower)
                    {
                        // Appeler la création de la notification et mettre en param getClassObject
                    }

                }
            }
        }
    }
}

?>