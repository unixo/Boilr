<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\InterventionCheck
 *
 * @ORM\Table(name="intervention_check")
 * @ORM\Entity
 */
class InterventionCheck
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $parent;

    /**
     * @var TemplateItem
     *
     * @ORM\ManyToOne(targetEntity="TemplateItem")
     * @ORM\JoinColumn(name="template_item_id", referencedColumnName="id", nullable=false)
     * @ORM\Id
     */
    protected $templateItem;

    /**
     * @var string $id
     *
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $value;


    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set parent
     *
     * @param Boilr\BoilrBundle\Entity\InterventionDetail $parent
     */
    public function setParent(\Boilr\BoilrBundle\Entity\InterventionDetail $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Boilr\BoilrBundle\Entity\InterventionDetail
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set templateItem
     *
     * @param Boilr\BoilrBundle\Entity\TemplateItem $templateItem
     */
    public function setTemplateItem(\Boilr\BoilrBundle\Entity\TemplateItem $templateItem)
    {
        $this->templateItem = $templateItem;
    }

    /**
     * Get templateItem
     *
     * @return Boilr\BoilrBundle\Entity\TemplateItem
     */
    public function getTemplateItem()
    {
        return $this->templateItem;
    }
}