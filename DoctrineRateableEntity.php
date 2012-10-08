<?php
namespace ERD\RateableEntitiesBundle;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Provides some default and convenience methods for implementing Rateable objects.
 * 
 * To use this trait you must implement a getRawBusinessValue() method that returns the object's
 * business value without clipping it to the -.5 to .5 range by inspecting its RateableFactors.
 * 
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jun 30, 2012 Ethan Resnick Design
 */
trait DoctrineRateableEntity
{
    /**
     * @var decimal A score between 0 and 1, set manually
     * @ORM\Column(type="decimal", scale=7, precision=8)
     * @Assert\Max(1)
     * @Assert\Min(0)
     * @Assert\NotBlank()
     */
    protected $relevanceToUser;

    /**
     * @var decimal A score between 0 and 1, set manually
     * @ORM\Column(type="decimal", scale=7, precision=8)
     * @Assert\Max(1)
     * @Assert\Min(0)
     * @Assert\NotBlank()
     */
    protected $decayRate;

    /**
     * @var decimal Calculated automatically from the above properties; stored for easy access.
     * @ORM\Column(type="decimal", scale=7, precision=8)
     */
    protected $currentRating;

    protected $boost = 0;

    /**
     * This rounds the results of {@link getRawBusinessValue()}.
     * 
     * By moving all the real work into getRawBusinessValue(), this method can be the same on 
     * every Rateable class and ensure a valid public interface.
     */
    public function getBusinessValue()
    {
        return \max(-.5, \min(.5, $this->getRawBusinessValue()));
    }
    
    public function setBoost($boost)
    {
        $this->boost = $boost;
    }
    
    public function getBoost() 
    {
        return $this->boost;
    }
    
    /**
     * Sets the current rating. Can be called from a controller or cron script
     * with $time (i.e. number of content items published since) passed in; is
     * also called automatically on first saving with $time set to 0, its default.
     *
     * @param The number of entities published since this entity's relevant content age date
     * @ORM\prePersist
     */
    public function setCurrentRating($time=0)
    {
        $this->currentRating = ($this->getBusinessValue() + $this->getRelevanceToUser()) * exp(-1*$time*$this->getDecayRate());
    }
    
    public function getCurrentRating() 
    {
        return $this->currentRating;
    }

    public function setRelevanceToUser($relevanceToUser)
    {
        $this->relevanceToUser = $relevanceToUser;
    }

    public function getRelevanceToUser()
    {
        return $this->relevanceToUser;
    }

    public function setDecayRate($decayRate)
    {
        $this->decayRate = $decayRate;
    }

    public function getDecayRate()
    {
        return $this->decayRate;
    }

    abstract protected function getRawBusinessValue();
}
