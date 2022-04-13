<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Formation $entity, bool $flush = true): void
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
    public function remove(Formation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByQuery($query)
    {
        $qb = $this->createQueryBuilder('f');
        $formations = $qb
            ->where($qb->expr()->orX(
                $qb->expr()->like('f.title', $qb->expr()->literal('%'.$query.'%'))
            ))
            ->getQuery()
            ->getResult()
            ;

        return $formations;
    }

    /**
    * @return Formation[] Returns an array of Formation objects
    */
    public function findLearnerProgressForEachFormation($learnerId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT DISTINCT 
                formation.id, 
                COUNT(lesson_done.lesson_id) OVER (PARTITION BY formation.id) AS lessons_done,
                (SELECT COUNT(lesson.title) 
                    FROM section 
                    JOIN formation ON section.formation_id = formation.id
                    JOIN lesson ON lesson.section_id = section.id
                    WHERE formation.id = 29) AS all_lessons
            FROM section 
                JOIN formation ON section.formation_id = formation.id
                JOIN lesson ON lesson.section_id = section.id
                JOIN lesson_done ON lesson.id = lesson_done.lesson_id
            WHERE lesson_done.learner_id = :learner';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['learner' => $learnerId]);

        $lessonsDoneForEachFormation = $resultSet->fetchAllAssociative();

        $progressForEachFormation = [];
        foreach ($lessonsDoneForEachFormation as $formation) {
            $progress = 100*$formation['lessons_done']/$formation['all_lessons'];

            $progressForEachFormation[] = [
                'id' => $formation['id'],
                'progress' => $progress,
            ];
        }

        return $progressForEachFormation;
    }

    /*
    public function findOneBySomeField($value): ?Formation
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
