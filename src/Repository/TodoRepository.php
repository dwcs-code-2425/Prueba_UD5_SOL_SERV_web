<?php

namespace App\Repository;

use App\Entity\Todo;
use App\Model\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Todo>
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em)
    {
        parent::__construct($registry, Todo::class);
    }

    public function save(Todo $todo, bool $flush){
        $this->em->persist($todo);
        if($flush){
            $this ->em->flush();
        }
    }

    public function remove(Todo $todo, bool $flush){
        $this->em->remove($todo);
        if($flush){
            $this ->em->flush();
        }
    }

    public function findAllWithPagination(int $page): Paginator
{
    $query = $this->createQueryBuilder('t')->orderBy('t.createdAt', 'ASC');

    return new Paginator($query, $page);
}


    

    //    /**
    //     * @return Todo[] Returns an array of Todo objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Todo
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
