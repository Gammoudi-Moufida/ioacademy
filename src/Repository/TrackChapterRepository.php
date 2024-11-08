<?php

namespace App\Repository;

use App\Entity\TrackChapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrackChapter|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackChapter|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackChapter[]    findAll()
 * @method TrackChapter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackChapterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackChapter::class);
    }

}
