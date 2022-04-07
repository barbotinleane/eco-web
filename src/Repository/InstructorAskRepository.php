<?php

namespace App\Repository;

use App\Entity\InstructorAsk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InstructorAsk|null find($id, $lockMode = null, $lockVersion = null)
 * @method InstructorAsk|null findOneBy(array $criteria, array $orderBy = null)
 * @method InstructorAsk[]    findAll()
 * @method InstructorAsk[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstructorAskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InstructorAsk::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(InstructorAsk $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(InstructorAsk $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return InstructorAsk[] Returns an array of InstructorAsk objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InstructorAsk
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
