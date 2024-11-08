<?php

namespace App\Repository;

use App\Entity\TrackFormation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrackFormation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackFormation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackFormation[]    findAll()
 * @method TrackFormation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackFormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackFormation::class);
    }

    public function findDetail($userid)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->select(['t', 'f', 'u']) // récupération des alias obligatoire pour que la jointure soit effective
           ->from(TrackFormation::class, 't')
           ->leftJoin('t.formation', 'f')
           ->leftJoin('t.user', 'u')
           ->where('u.id = :id')
           ->setParameter('id', $userid);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

}
