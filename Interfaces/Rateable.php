<?php
namespace ERD\RateableEntitiesBundle\Interfaces;

/**
 * The interface implementated by any entity in the model that can be rated
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright 2011 Ethan Resnick Design
 * 
 */
interface Rateable {
    
    public function getRelevanceToUser();
    public function setRelevanceToUser($score);

    public function setDecayRate($score);
    public function getDecayRate();
    
    /**
     * Returns the element's business value, calculated directly from its contents (mostly from 
     * its RateableFactors), bounded to between -.5 and .5. 
     * @return decimal A value between -.5 and .5, centered roughly on 0, derived 
     *                 automatically from the content's RateableFactors.
     */
    public function getBusinessValue();
    
    /**
     * Returns a "boost" value, which allows for a manual boost (usually temporary) to a given piece of content.
     * 
     * The boost is added in to generate the value returned by getCurrentRating()
     */
    public function getBoost();
    public function setBoost($boost);
    
    /**
     * Different elements should use different properties to note when their decay starts. E.g.,
     * for events the dates of the event might be much more meaningful than when the event was 
     * added to the system or published live. Then again, the published date might not be 
     * irrelevant (how much time have viewers had to see this?), so maybe you average the two. 
     * Who knows. The point is that there's not a universal way to calculate the appropriate 
     * decay starting date from a given content element. So this method below forces the object 
     * to specify one..
     * @return \DateTime The date for when this element's decay starts and calculating how much it has progressed.
     */
    public function getRelevantContentAgeDate();
    
    public function getCurrentRating();
    public function setCurrentRating($time);

}
