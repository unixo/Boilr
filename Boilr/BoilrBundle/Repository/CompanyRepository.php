<?php

namespace Boilr\BoilrBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Boilr\BoilrBundle\Entity\Company;

/**
 * Description of CompanyRepository
 *
 * @author unixo
 */
class CompanyRepository extends EntityRepository
{
    public function getEmployees(Company $company)
    {
        $criteria = array('company' => $company->getId());
        $sortBy = array('surname' => 'ASC', 'name' => 'ASC');
        $employees = $this->getEntityManager()->getRepository('BoilrBundle:Installer')
                          ->findBy($criteria, $sortBy);

        return $employees;
    }
}
