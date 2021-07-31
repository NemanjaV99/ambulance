<?php

namespace App\Repository;

use App\Entity\Doctor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Doctor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Doctor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Doctor[]    findAll()
 * @method Doctor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Doctor::class);
    }

    /**
     * @return Doctor[] Returns an array of Doctor objects joined with data from User, TypeDoctor
     */
    public function findAllJoinedToTypeAndUser()
    {
        $manager = $this->getEntityManager();

        $query = $manager->createQuery(
            'SELECT 
                d.id AS doctor_id,
                d.firstName AS doctor_fname,
                d.lastName AS doctor_lname,
                u.username AS doctor_user_username,
                t.name AS doctor_type
            FROM
                App\Entity\Doctor d
            INNER JOIN d.user u
            INNER JOIN d.type t'
        );

        return $query->getResult();


        /*
        $qb = $this->createQueryBuilder('d')
            ->innerJoin('d.user', 'u')
            ->innerJoin('d.type', 't')
            ->getQuery()
            ->getResult();

        return $qb;
        */
    }

    // /**
    //  * @return Doctor[] Returns an array of Doctor objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Doctor
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
