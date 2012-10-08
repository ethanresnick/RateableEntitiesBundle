<?php
namespace ERD\RateableEntitiesBundle\Twig\Extension;
use ERD\RateableEntitiesBundle\Form\DataTransformer\NumberToPresentableRatingTransformer;
/**
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Oct 7, 2012 Ethan Resnick Design
 */
class RatingFormatterExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'format_rating'  => new \Twig_Filter_Method($this, 'ratingFormat', array('is_safe' => array('html')))
        );
    }

    public function ratingFormat($number, $range='positive')
    {
        $transformer = new NumberToPresentableRatingTransformer($range);

        return $transformer->transform($number);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'erd_rating_formatter';
    }
}