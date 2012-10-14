<?php
namespace ERD\RateableEntitiesBundle;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


trait DoctrineRateableFactorEntity
{
    /**
     * @var decimal Should be between -.5 and .5, with an average of 0 among all content elements
     * @ORM\Column(type="decimal", scale=7, precision=8)
     * @Assert\NotBlank()
     * @Assert\Min("-.5")
     * @Assert\Max(".5")
     */
    protected $businessValue;

    public function getBusinessValue()
    {
        return $this->businessValue;
    }

    public function setBusinessValue($value)
    {
        $this->businessValue = $value;
    }
}
?>