<?php
namespace ERD\RateableEntitiesBundle\Doctrine\Event;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use ERD\DoctrineHelpersBundle\Provider\DoctrineEntityProvider;
use Symfony\Bundle\DoctrineBundle\Registry;
use ERD\RateableEntitiesBundle\Interfaces\Rateable;
use ERD\RateableEntitiesBundle\Interfaces\RateableFactor;
/**
 * Updates all rateable entities when one managed entity is persisted or updated.
 * 
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jun 16, 2012 Ethan Resnick Design
 */
class RateableSubscriber implements \Doctrine\Common\EventSubscriber
{
    /** @var DoctrineEntityProvider */
    protected $provider;
    
    /** @var Registry */
    protected $doctrineRegistry;

    /**
     * Constructor
     *
     * @param Registry $doctrineRegistry Doctrine.
     * @param DoctrineEntityProvider $provider A provider that returns all the rateable entities.
     */
    public function __construct(Registry $doctrineRegistry, DoctrineEntityProvider $provider)
    {
        $this->doctrineRegistry = $doctrineRegistry;
        $this->provider = $provider;
    }

    /**
     * @todo Faster with onFlush?
     */
    public function getSubscribedEvents()
    {
        return array(Events::preUpdate, Events::prePersist, Events::preRemove);
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        //If we're persisting an element in the rating system we always have
        //to update all the ratings because it's likely that our new element
        //changed how much they've decayed.
        if($this->isRateable($eventArgs->getEntity()))
        {
            $this->updateRatings();
        }
    }

    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        //If we're removing an element in the rating system we always have
        //to update all the ratings because it's likely that our new element
        //changed how much they've decayed.
        if($this->isRateable($eventArgs->getEntity()))
        {
            $this->updateRatings();
        }
    }

    public function preUpdate(\Doctrine\ORM\Event\PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $changeSet = $eventArgs->getEntityChangeSet();

        /* This condition updates the object if it's rateable and something related to its rating has changed.
         *
         * @todo Right now, the second part of the condition (something about its rating has changed) is always
         * evaluated to true. This is because not only are the literal properties (relevanceToUser etc) related to the
         * rating, but so are the results of the getRelevantContentAge() and getBusinessValue() methods...and there's
         * no easy way to see if those results have changed--Doctrine only tells us what properties have changed, and
         * we don't know what properties those methods depend on. So we have two options:
         *
         *  1) a config setting specifying which properties trigger update (probably a good idea anyway so the basic
         *     rating properties can be renamed without causing problems.
         *
         *  2) We could clone the modified entity, reset all its changed properties with reflection, call the methods
         *     on the "reverted" clone to get their original values and then call them on the entity in its current
         *     state to see if they've changed.
         */
        if($this->isRateable($entity) &&
           (count(array_intersect(array('relevanceToUser', 'decayRate', 'businessValue'), array_keys($changeSet))) > 0) || (1==1))
        {
            $this->updateRatings($entity);
        }
    }

    protected function isRateable($entity)
    {
       return ($entity instanceof Rateable && !$entity instanceof RateableFactor);
    }

    /**
     * @param $updateArgs The entity we're updating with this event, if any. Needed because Doctrine handles making
     * changes to updated elements in a listener differently than it does making changes to new elements.
     */
    protected function updateRatings(\Doctrine\ORM\Event\PreUpdateEventArgs $updateArgs = null)
    {
        $entities = $this->provider->getAllEntities();
        
        \usort($entities, 
            function($a, $b) 
            {
                $arca = $a->getRelevantContentAgeDate();
                $brca = $b->getRelevantContentAgeDate();
                if($arca==$brca) { return 0; }
                return ($arca > $brca) ? -1 : 1;
            });

        if(!$updateArgs)
        {
            foreach($entities as $numberPublishedSince=>$entity)
            {
                $entity->setCurrentRating($numberPublishedSince);
                //$this->doctrineRegistry->getEntityManagerForClass(get_class($entity))->persist($entity);
            }
        }

        else
        {
            $updatingEntity = $updateArgs->getEntity();

            foreach($entities as $numberPublishedSince=>$entity)
            {
                $entity->setCurrentRating($numberPublishedSince);

                if($entity==$updatingEntity) {
                    $newRating = $entity->getCurrentRating();
                    $updateArgs->setNewValue('currentRating', $newRating);
                }
            }
        }
        
        return $entities;
    }
}