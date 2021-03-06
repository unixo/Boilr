<?php

namespace Boilr\BoilrBundle\Policy;

use Boilr\BoilrBundle\Service\GeoPosition,
    Boilr\BoilrBundle\Entity\Address,
    Boilr\BoilrBundle\Form\Model\InstallerForIntervention;

/**
 * Description of WaypointPolicy
 *
 * @author unixo
 */
class WaypointPolicy extends BasePolicy
{

    /**
     * {@inheritDoc}
     */
    public static function getName()
    {
        return "policy_waypoint";
    }

    /**
     * {@inheritDoc}
     */
    public static function getDescription()
    {
        return "Percorso ottimale degli interventi";
    }

    /**
     * {@inheritDoc}
     */
    public function elaborate()
    {
        $this->result->setResultType(PolicyResult::RESULT_SCHEDULE_TIME);

        // get company which current user belongs to
        $company = $this->user->getCompany();

        foreach ($this->interventions as $day => $interventions) {
            $count = count($interventions);
            if ($count == 1) {
                $assoc = new InstallerForIntervention();
                $assoc->setIntervention($interventions[0]);
                $aDate = clone $interventions[0]->getScheduledDate();
                $aDate->setTime(8, 0);
                $assoc->setNewScheduledDate($aDate);

                $this->result->addAssociation($assoc);
            } else {
                // starting point
                $src = $company->getGeoPosition();
                // list of interventions of current day, ordered by best route
                $ordered = $this->orderInterventionsByWaypoints($src, $interventions);

                // first step
                $firstInterv = array_shift($ordered);

                $assoc = new InstallerForIntervention();
                $assoc->setIntervention($firstInterv);
                $aDate = clone $firstInterv->getScheduledDate();
                $aDate->setTime(8, 0);
                $assoc->setNewScheduledDate($aDate);
                $this->result->addAssociation($assoc);

                $lastAssoc = $assoc;
                /* @var $lastInterv InstallerForIntervention */

                foreach ($ordered as $interv) {
                    // get scheduled date of last association and add intervention duration
                    $lastDate = clone $lastAssoc->getNewScheduledDate();
                    $lastDate->add($lastAssoc->getIntervention()->getExpectedTimeLength());

                    // eval distance from position of last intervention to next intervention address
                    $lastGeoPos = $lastAssoc->getIntervention()->getFirstSystem()->getAddress()->getGeoPosition();
                    $nextGeoPos = $interv->getFirstSystem()->getAddress()->getGeoPosition();
                    $result = $this->directionHelper->getSingleDirections($lastGeoPos, $nextGeoPos);

                    // add travel time: this will scheduled date of next intervention
                    $newDate = $lastDate->add(\DateInterval::createFromDateString($result['length']));

                    // add association
                    $assoc = new InstallerForIntervention();
                    $assoc->setIntervention($interv);
                    $assoc->setNewScheduledDate($newDate);
                    $this->result->addAssociation($assoc);

                    $lastAssoc = $assoc;
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(PolicyResult $result)
    {
        $this->entityManager->beginTransaction();

        $success = true;

        try {
            foreach ($result->getAssociations() as $assoc) {
                /* @var $assoc \Boilr\BoilrBundle\Form\Model\InstallerForIntervention */
                if ($assoc->getChecked() == true) {
                    $interv = $assoc->getIntervention();
                    $newDate = $assoc->getNewScheduledDate();
                    $interv->setScheduledDate($newDate);
                }
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $exc) {
            $this->entityManager->rollback();
            $success = false;
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        $assocs = $this->result->getAssociations();
        usort($assocs, array("\Boilr\BoilrBundle\Form\Model\InstallerForIntervention", "sortByNewScheduledDate"));
        $this->result->setAssociations($assocs);

        return $this->result;
    }

    protected function findFarthestIntervention(GeoPosition $source, $interventions)
    {
        $sources = array($source);

        $destinations = array();
        foreach ($interventions as $interv) {
            // @var $interv \Boilr\BoilrBundle\Entity\MaintenaceIntervention //
            $destinations[] = $interv->getFirstSystem()->getAddress()->getGeoPosition();
        }

        $result = $this->directionHelper->getMultipleDirections($sources, $destinations);

        $index = -1;
        $maxDistance = 0;
        for ($i = 0; $i < count($result->rows[0]->elements); $i++) {
            $item = $result->rows[0]->elements[$i];

            if ($item->distance->value > $maxDistance) {
                $index = $i;
                $maxDistance = $item->distance->value;
            }
        }

        return $interventions[$index];
    }

    /**
     * The function gets a source address and a list of addresses: it finds the
     * farthest location and set it as destination; then ask Google to find a
     * route between source & destination, using all other addresses as
     * waypoints
     *
     * @param GeoPosition $source
     * @param array $interventions
     * @return array
     */
    protected function orderInterventionsByWaypoints(GeoPosition $source, $interventions)
    {
        // ending point (farthest intervetion)
        $farthestInterv = $this->findFarthestIntervention($source, $interventions);
        $dst = $farthestInterv->getFirstSystem()->getAddress()->getGeoPosition();

        // waypoints: all interventions except the farthest
        $waypoints = array();
        $linkedIntervs = array();
        foreach ($interventions as $interv) {
            if ($interv != $farthestInterv) {
                $waypoints[] = $interv->getFirstSystem()->getAddress()->getGeoPosition();
                $linkedIntervs[] = $interv;
            }
        }
        assert(count($waypoints) == count($linkedIntervs));

        // find best route
        $orderedInterventions = array();
        $orderedGeoPoints = $this->directionHelper->findBestRoute($source, $dst, $waypoints);
        $first = array_shift($orderedGeoPoints); // pop source out of array
        assert($first == $source);

        // build ordered intervention list based on route steps
        foreach ($orderedGeoPoints as $geoPoint) {
            $index = array_search($geoPoint, $waypoints);
            assert(is_numeric($index));
            $orderedInterventions[] = $linkedIntervs[$index];

            array_splice($waypoints, $index, 1);
            array_splice($linkedIntervs, $index, 1);
        }
        // add destination again
        $orderedInterventions[] = $farthestInterv;

        return $orderedInterventions;
    }

    private function dumpIntervention($interv)
    {
        $str = $interv->getId() . ", " . $interv->getScheduledDate()->format('d-m-Y') . ", ";
        $str .= $interv->getFirstSystem()->getAddress()->getAddress();
        var_dump($str);
    }

}
