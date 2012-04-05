<?php

namespace Boilr\BoilrBundle\Entity;

use Doctrine\ORM\Mapping as ORM,
    Symfony\Component\Validator\Constraints as Assert;

/**
 * Boilr\BoilrBundle\Entity\Config
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="Boilr\BoilrBundle\Repository\ConfigRepository")
 */
class Config
{
    const KEY_WORKDAY_START  = 'workday_start';
    const KEY_WORKDAY_END    = 'workday_end';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $parameter
     *
     * @ORM\Column(name="parameter", type="string", length=100, nullable=false, unique=true)
     */
    private $setting;

    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getSetting()
    {
        return $this->setting;
    }

    public function setSetting($setting)
    {
        $this->setting = $setting;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}