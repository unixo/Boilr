<?php

namespace Boilr\BoilrBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;
use Boilr\BoilrBundle\Entity\System,
    Boilr\BoilrBundle\Entity\MaintenanceIntervention,
    Boilr\BoilrBundle\Entity\Config;

/**
 * Description of RandomDataCommand
 *
 * @author unixo
 */
class RandomDataCommand extends ContainerAwareCommand
{

    /**
     * @var \Symfony\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    protected function configure()
    {
        $this
                ->setName('boilr:data')
                ->setDescription('create random data for test purposes')
                ->addOption('systems', null, InputOption::VALUE_NONE, 'Add systems to customer')
                ->addOption('interventions', null, InputOption::VALUE_NONE, 'Create manteinance interventions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        if ($input->getOption('systems')) {
            $this->createSystems($output);
        } else if ($input->getOption('interventions')) {
            $this->createInterventions($output);
        }
    }

    protected function createInterventions(OutputInterface $output)
    {
        $start = $this->doctrine->getRepository('BoilrBundle:Config')->getValue(Config::KEY_WORKDAY_START);
        $end = $this->doctrine->getRepository('BoilrBundle:Config')->getValue(Config::KEY_WORKDAY_END);

        $entries = explode(":", $start);
        $hhStart = (integer) $entries[0];
        $entries = explode(":", $end);
        $hhEnd = (integer) $entries[0];

        // Start of period
        $startDate = new \DateTime();
        $startDate->sub(\DateInterval::createFromDateString('1 month'));
        $year = $startDate->format('Y');
        $month = $startDate->format('m');
        $startDate->setDate($year, $month, 1);

        // End of period
        $monthName = $startDate->format('F');
        $lastDay = date("d", strtotime("last day of $monthName $year"));
        $endDate = new \DateTime();
        $endDate->setDate($year, $month, $lastDay);

        // Operation groups
        $opGroups = $this->doctrine->getRepository('BoilrBundle:OperationGroup')->findAll();
        $opGroupCount = count($opGroups);

        $systems = $this->doctrine->getRepository('BoilrBundle:System')->findAll();
        foreach ($systems as $system) {
            for ($i=0; $i<2; $i++) {
                $hour = mt_rand($hhStart, $hhEnd);

                $randomDate = new \DateTime();
                $randomDate->setDate($year, $month, rand() % $lastDay);
                $randomDate->setTime($hour, 0, 0);
                $lateRandomDate = clone $randomDate;
                $lateRandomDate->add(\DateInterval::createFromDateString("1 hour"));

                $int = MaintenanceIntervention::UnplannedInterventionFactory();
                $int->setCustomer($system->getOwner());
                $int->addSystem($system, $opGroups[rand() % $opGroupCount]);
                $int->setScheduledDate($randomDate);
                $int->setExpectedCloseDate($lateRandomDate);
                $this->doctrine->getEntityManager()->persist($int);
            }
        }
        $this->doctrine->getEntityManager()->flush();
    }

    protected function createSystems(OutputInterface $output)
    {
        $aDate1 = new \DateTime();
        $aDate1->setDate(2000, 01, 01);
        $aDate2 = new \DateTime();
        $aDate2->setDate(2000, 01, 02);

        $systemTypes = $this->doctrine->getRepository('BoilrBundle:SystemType')->findAll();
        $systemTypeCount = count($systemTypes);

        $products = $this->doctrine->getRepository('BoilrBundle:Product')->findAll();
        $productCount = count($products);

        $addresses = $this->doctrine->getRepository('BoilrBundle:Address')->findAll();

        for ($i = 0; $i < count($addresses); $i++) {
            $newSystem = new System();
            $newSystem->setCode("matricola#" . $i);
            $newSystem->setDescr('impianto#' . $i);
            $newSystem->setSystemType($systemTypes[rand() % $systemTypeCount]);
            $newSystem->setProduct($products[rand() % $productCount]);
            $newSystem->setAddress($addresses[$i]);
            $newSystem->setOwner($addresses[$i]->getPerson());
            $newSystem->setInstallDate($aDate1);
            $newSystem->setLastMaintenance($aDate2);

            if (rand() % 2) {
                $installers = $newSystem->getSystemType()->getInstallers();
                $installerCount = count($installers);
                $newSystem->setDefaultInstaller($installers[rand() % $installerCount]);
            }

            $this->doctrine->getEntityManager()->persist($newSystem);
        }
        $this->doctrine->getEntityManager()->flush();
    }

}
