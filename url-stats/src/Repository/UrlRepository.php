<?php

namespace App\Repository;

use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Url>
 *
 * @method Url|null find($id, $lockMode = null, $lockVersion = null)
 * @method Url|null findOneBy(array $criteria, array $orderBy = null)
 * @method Url[]    findAll()
 * @method Url[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Url::class);
    }

    public function add(Url $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Url $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    */
   public function countUniqueByDomain(string $domain): int
   {
       return (int) $this->createQueryBuilder('u')
           ->select('count(u.id)')
           ->where("u.url LIKE :domain")
           ->setParameter('domain', "{$domain}%")
           ->groupBy('u.url')
           ->getQuery()
           ->getOneOrNullResult();
   }

   /**
    */
   public function countUniqueByPeriod(string $from, string $to): int
   {
       return (int) $this->createQueryBuilder('u')
           ->select('count(u.id)')
           ->andWhere('u.createdDate >= :from')
           ->andWhere('u.createdDate <= :to')
           ->setParameter('from', $from)
           ->setParameter('to', $to)
           ->groupBy('u.url')
           ->getQuery()
           ->getOneOrNullResult();
   }


//    /**
//     * @return Url[] Returns an array of Url objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Url
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
