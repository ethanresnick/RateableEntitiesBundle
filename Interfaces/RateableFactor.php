<?php
namespace ERD\RateableEntitiesBundle\Interfaces;


/**
 * The interface implemented by any entity whose instantiation is linked to a
 * content object and use to influence that object's rating (e.g. venues & subjects)
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright 2011 Ethan Resnick Design
 * 
 */
interface RateableFactor {
    
    public function getBusinessValue();
    
    /** @param decimal $businessValue Set as -.5 >= x <= .5, with 0 being the site average. */
    public function setBusinessValue($relevance);
}