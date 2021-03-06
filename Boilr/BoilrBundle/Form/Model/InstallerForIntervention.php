<?php

namespace Boilr\BoilrBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Boilr\BoilrBundle\Entity\Installer,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention;

class InstallerForIntervention
{

    /**
     * @var \Boilr\BoilrBundle\Entity\Installer
     * @Assert\Valid
     */
    protected $installer;

    /**
     * @var \Boilr\BoilrBundle\Entity\MaintenanceIntervention
     * @Assert\Valid
     */
    protected $intervention;

    protected $prevLoad;
    protected $newLoad;
    protected $checked;

    /**
     * New scheduled date for intervention
     *
     * @var \DateTime
     */
    protected $newScheduledDate;

    function __construct($installer = null, $intervention = null)
    {
        $this->installer = $installer;
        $this->intervention = $intervention;
        $this->checked = true;
        $this->newScheduledDate = null;
    }

    public function getNewScheduledDate()
    {
        return $this->newScheduledDate;
    }

    public function setNewScheduledDate($newScheduledDate)
    {
        $this->newScheduledDate = $newScheduledDate;
    }

    public function getInstaller()
    {
        return $this->installer;
    }

    public function setInstaller($installer)
    {
        $this->installer = $installer;
    }

    public function getIntervention()
    {
        return $this->intervention;
    }

    public function setIntervention($intervention)
    {
        $this->intervention = $intervention;
    }

    public function getPrevLoad()
    {
        return $this->prevLoad;
    }

    public function setPrevLoad($prevLoad)
    {
        $this->prevLoad = $prevLoad;
    }

    public function getNewLoad()
    {
        return $this->newLoad;
    }

    public function setNewLoad($newLoad)
    {
        $this->newLoad = $newLoad;
    }

    public function getChecked()
    {
        return $this->checked;
    }

    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    static function sortByScheduledDate(InstallerForIntervention $res1, InstallerForIntervention $res2)
    {
        $date1 = $res1->getIntervention()->getScheduledDate();
        $date2 = $res2->getIntervention()->getScheduledDate();

        if ($date1 == $date2) {
            return 0;
        }

        return ($date1 < $date2)?-1:1;
    }

    static function sortByNewScheduledDate(InstallerForIntervention $res1, InstallerForIntervention $res2)
    {
        $date1 = $res1->getNewScheduledDate();
        $date2 = $res2->getNewScheduledDate();

        if ($date1 == $date2) {
            return 0;
        }

        return ($date1 < $date2)?-1:1;
    }
}
