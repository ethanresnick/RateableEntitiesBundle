<?php
namespace ERD\RateableEntitiesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use ERD\RateableEntitiesBundle\Form\DataTransformer\NumberToPresentableRatingTransformer;

/**
 * Stores ratings, which need a custom type to facilitate translation between
 * pretty values (0 to 100 and -100 to 100) and the complex values stored in the
 * db (0-1, -.5 to .5, and -.5 to 1.5).
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jan 15, 2012 Ethan Resnick Design
 * @todo Inject the transformer if possible (rather than newing it). Ditto in the Twig extension that uses it.
 */
class RatingType extends AbstractType
{
    /**
     * For ratings defined on a 0-1 scale in the database,
     * like the base rating value of Rateable objects.
     */
    const RANGE_POSITIVE = 'positive';

    /**
     * For ratings defined between -.5 and .5, meant to average at 0,
     * like the businessValue component of RateableFactor.
     */
    const RANGE_AVERAGE = 'average';

    /**
     * For the current rating of a Rateable entity, which in it's base implementation,
     * is a decayed version of the entities base rating + it's business value, and can
     * therefore range between -.5 and 1.5
     */
    const RANGE_AGGREGATE = 'aggregate';

    public function buildView(FormView $view, FormInterface $form)
    {
        $view->setAttribute('min', 0);
        $view->setAttribute('max', 100);
        $view->setAttribute('type','range');
        $view->setAttribute('class','erd_rating');
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $transformer = new NumberToPresentableRatingTransformer($options['range']);
        $builder->appendClientTransformer($transformer);
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'range'=> isset($options['range']) ? $options['range'] : static::RANGE_POSITIVE
        );
    }

    public function getParent(array $options)
    {
        return 'number';
    }

    public function getName()
    {
        return 'erd_rating';
    }

}