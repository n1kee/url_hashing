<?php

namespace App\Repository;

use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    public function findOneByHash(string $value): ?Url
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.hash = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByUrl(string $value): ?Url
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.url = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getAllUnreported(): ?array
    {
        return $this->createQueryBuilder('u')
            ->select(['u.id', 'u.url', 'u.createdDate'])
            ->andWhere('u.is_reported IS NULL')
            ->orWhere('u.is_reported <> 1')
            ->getQuery()
            ->getResult()
        ;
    }

    public function markAsReported(array $urlIds): int
    {
        return $this->createQueryBuilder('u')
            ->update()
            ->set('u.is_reported', 1)
            ->where("u.id IN (:urlIds)")
            ->setParameter('urlIds', $urlIds)
            ->getQuery()
            ->execute()
        ;
    }
}
