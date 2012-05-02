<?php

namespace Boilr\BoilrBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ManteinanceInterventionFilter
{

    /**
     * @var boolean
     * Assert\Type(type="bool")
     */
    protected $searchByDate;

    /**
     * @var \DateTime
     * @Assert\Date
     */
    protected $startDate;

    /**
     * @var \DateTime
     * @Assert\Date
     */
    protected $endDate;

    /**
     * @var boolean
     * Assert\Type(type="bool")
     */
    protected $planned;

    /**
     * @var boolean
     * Assert\Type(type="bool")
     */
    protected $withoutInstaller;

    /**
     * @var array
     */
    protected $status;

    function __construct()
    {
        $now = new \DateTime();
        $nextMonth = clone $now;
        $nextMonth->add(\DateInterval::createFromDateString('1 month'));

        $this->status = array();
        $this->searchByDate = true;
        $this->withoutInstaller = false;
        $this->startDate = $now;
        $this->endDate = $nextMonth;
    }

    public function getSearchByDate()
    {
        return $this->searchByDate;
    }

    public function setSearchByDate($searchByDate)
    {
        $this->searchByDate = $searchByDate;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate = null)
    {
        if ($startDate) {
            $this->startDate = $startDate;
        }
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate = null)
    {
        if ($endDate) {
            $this->endDate = $endDate;
        }
    }

    public function getPlanned()
    {
        return $this->planned;
    }

    public function setPlanned($planned)
    {
        $this->planned = $planned;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status = array())
    {
        $this->status = $status;
    }

    public function getWithoutInstaller()
    {
        return $this->withoutInstaller;
    }

    public function setWithoutInstaller($withoutInstaller)
    {
        $this->withoutInstaller = $withoutInstaller;
    }

}
