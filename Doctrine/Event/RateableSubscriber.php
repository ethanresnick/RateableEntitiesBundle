<?php
namespace ERD\RateableEntitiesBundle\Doctrine\Event;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use ERD\DoctrineHelpersBundle\Provider\DoctrineEntityProvider;
use Symfony\Bundle\DoctrineBundle\Registry;
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
     * @param DoctrineEntityProvider A provider that returns all the rateable entities.
     */
    public function __construct(Registry $doctrineRegistry, DoctrineEntityProvider $provider)
    {
        $this->doctrineRegistry = $doctrineRegistry;
        $this->provider = $provider;
    }
    
    public function getSubscribedEvents()
    {
        return array(Events::onFlush);
    }

    /**
     * Updates the ratings of all entities (from the provider) onFlush.
     * 
     * Must update on flush because updating prePersist/preUpdate would cause a loop (at least 
     * with the standard $em->persist, though maybe not with the UOW API) as the elements whose
     * ratings we're updating would themselves have to be persisted, again triggering all 
     * elements to be updated and persisted. OnFlush prevents this because it's always the last
     * thing to occur, and happens at the transaction/EM, rather than entity, level.
     * 
     * OnFlush may also have performance gains. One problem with it though is that it requires
     * working with the UOW API directly to save changes, which is slightly more complicated. 
     * 
     * But the real complication comes in because we can have multiple entity managers, but only
     * one is being flushed... So we need to update all its Rateable entities with UOW, but also 
     * find the other EMs and update their Rateable entities but through the standard 
     * EM->persist() method.
     * 
     * @param OnFlushEventArgs $eventArgs 
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entities  = $this->getRatedEntities();
        $flushedEm = $eventArgs->getEntityManager();
        $flushedEmClasses = $ourEm->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();

        //if we only have one em, so no need to detect which em each element belongs to. Much faster.
        if(count($this->doctrineRegistry->getEntityManagers())==1)
        {
            foreach($entities as $entity)
            {
                //persist through UOW API
            }            
        }
        
        else
        {
            foreach($entities as $entity)
            {
                $class = get_class($entity);
                
                if(in_array($class, $flushedEmClasses))
                {
                    //persist with UOW API
                }
            
                else
                {
                    $em = $this->doctrineRegistry->getEntityManagerForClass($class);
                    $em->persist($entity); //will actually be applied when this EM ($em) is flushed.
                }
            }
        }
    }

    protected function getRatedEntities()
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
        
        foreach($entities as $numberPublishedSince=>$entity)
        {
            $entity->setCurrentRating($numberPublishedSince);
            //$this->doctrineRegistry->getEntityManagerForClass(get_class($entity))->persist($entity);
        }
        
        return $entities;
    }
}