<?php

namespace App\Repository;

use App\Entity\Examination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Examination|null find($id, $lockMode = null, $lockVersion = null)
 * @method Examination|null findOneBy(array $criteria, array $orderBy = null)
 * @method Examination[]    findAll()
 * @method Examination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExaminationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Examination::class);
    }

    public function findAllJoinedToDoctorAndPatient()
    {
        $manager = $this->getEntityManager();

        $query = $manager->createQuery(
            'SELECT 
                e.id AS id,
                e.date AS date,
                e.diagnosis AS diagnosis,
                e.performed AS performed,
                p.firstName AS patient_fname,
                p.lastName AS patient_lname,
                p.jmbg AS patient_jmbg,
                d.id AS doctor_id,
                d.firstName AS doctor_fname,
                d.lastName AS doctor_lname
            FROM
                App\Entity\Examination e
            INNER JOIN e.patient p
            INNER JOIN e.doctor d'
        );

        return $query->getResult();
    }

    // /**
    //  * @return Examination[] Returns an array of Examination objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Examination
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
