<?php
namespace ERD\RateableEntitiesBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\DataTransformerInterface;
use ERD\RateableEntitiesBundle\Form\Type\RatingType;

/**
 * Description of RatingToNumberTransformer
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jan 15, 2012 Ethan Resnick Design
 */
class NumberToPresentableRatingTransformer implements DataTransformerInterface
{
    protected $validRanges = array(RatingType::RANGE_POSITIVE, RatingType::RANGE_AVERAGE, RatingType::RANGE_AGGREGATE);

    public function __construct($range)
    {
        if(!in_array($range, $this->validRanges))
        {
            throw new TransformationFailedException("The range option you provided ({$range}) is invalid. It must be one of: ".implode(', ',$this->validRanges).".");
        }
        
        $this->range = $range;
    }

    // transforms the raw number to a form value
    public function transform($number)
    {
        if (null === $number) { return ''; }
        
        if($this->range==RatingType::RANGE_AGGREGATE)
        {
            $number = ($number + .5)/2; //to get it between 0 and 1, rather than -.5 and 1.5
        }
        elseif($this->range==RatingType::RANGE_AVERAGE)
        {
            $number += .5; //to get it between 0 and 1, rather than -.5 and .5
        }

        $number *= 100; //scale all types by 100
        
        return $number;
    }

    // transforms the form value to a raw number
    public function reverseTransform($val)
    {
        if ($val===false || $val === null) { return ''; }
        
        if($this->range==RatingType::RANGE_AGGREGATE)
        {
            $val = ($val*2) - 50; //to get it between -50 and 150
        }
        elseif($this->range==RatingType::RANGE_AVERAGE)
        {
            $val -= 50; //to get it between -50 and 50 
        }
        
        $val /= 100; //divide all types by 100
        
        return (string) $val;
    }
}