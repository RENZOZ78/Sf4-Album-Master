<?php

namespace App\Repository;

use App\Entity\Record;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[]    findAll()
 * @method Record[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    /**
     * Nouveautés: albums sortis il y a moins d'un mois
     */
    public function findNews()
    {
        return $this->createQueryBuilder('r')       // r = alias de Record
            ->where('r.releasedAt >= :last_month')
            ->setParameter('last_month', new \DateTime('-1 month'))
            ->orderBy('r.releasedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Top 10: les 10 albums les mieux notés sortis il y a moins d'un an
     */
    public function topTen()
    {
        return $this->createQueryBuilder('r')
            ->addSelect('AVG(n.value) AS HIDDEN average')
            ->innerJoin('r.notes', 'n')
            ->where('r.releasedAt >= :last_year')
            ->setParameter('last_year', new \DateTime('-1 year'))
            ->groupBy('r')
            ->orderBy('average', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
