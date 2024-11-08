<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('id' => 'ASC'));
    }
    
    public function getFormationByChampNotActive()
    {
        return $this->createQueryBuilder('f')
            ->where('f.active != 3')
            ->getQuery()
            ->getResult()
        ;
    }
    public function getFormationByChampActive()
    {
        return $this->createQueryBuilder('f')
            ->where('f.active = 3')
            ->getQuery()
            ->getResult()
        ;
    }
    public function getFormationByCategory(int $category)
    {
        return $this->createQueryBuilder('f')
            ->where('f.category ='. $category)
            ->andWhere('f.active = 3')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findMatching(string $query)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('LOWER(f.title) LIKE :query')
            ->andWhere('f.active = 3')
            ->setParameter('query', '%'.strtolower($query).'%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
    public function getFirstsActiveFormation()
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.active = 3')
            ->setFirstResult(0)
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();
    }
    
    public function formSearch ($level, $category)
    {
        $query = $this->createQueryBuilder('f')
                      ->andWhere('f.active = 3');
        if ($level)
        {  
            $query
                  ->andWhere('f.skills LIKE :level')
                  ->setParameter('level',Formation::$levels[$level]);
        }
        if($category)
        {
            $query
                  ->andWhere('f.category ='. $category);
        }
        if($level && $category){
            $query
                  ->andWhere('f.skills LIKE :level')
                  ->setParameter('level',Formation::$levels[$level])
                  ->andWhere('f.category ='. $category);
        }
            return $query->getQuery()
                  ->getResult();
                
     
    }
}
