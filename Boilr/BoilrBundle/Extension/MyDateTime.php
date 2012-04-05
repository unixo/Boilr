<?php

namespace Boilr\BoilrBundle\Extension;


class MyDateTime extends \DateTime
{
    public static function nextWorkingDay(\DateTime $aDate)
    {
        $interv  = \DateInterval::createFromDateString('1 day');
        $weekDay = $aDate->format('N');

        while (in_array($weekDay, array(DayOfWeek::Saturday, DayOfWeek::Sunday)) ) {
            $aDate->add($interv);
            $weekDay = $aDate->format('N');
        }

        return $aDate;
    }

    /**
     * Returns true if given date represents a working day
     *
     * @param \DateTime $aDate
     * @return boolean
     */
    public static function isWorkingDay(\DateTime $aDate)
    {
        $weekDay = $aDate->format('N');
        $working = ! in_array($weekDay, array(DayOfWeek::Saturday, DayOfWeek::Sunday));

        return $working;
    }
}