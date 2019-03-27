<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

final class PostRepository implements PostRepositoryInterface
{
    /** @var EntityRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Post::class);
    }

    /** @inheritdoc */
    public function findAllPaginatedLatestFirst(int $page): Pagerfanta
    {
        $queryBuilder = $this->repository
            ->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->where('p.enabled = true')
        ;

        return $this->createPaginator($queryBuilder->getQuery(), $page);
    }

    /** @inheritdoc */
    public function findAllLatestFirstAlsoDisabled(): array
    {
        return $this->repository
            ->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /** @inheritdoc */
    public function findAllSelectOnlyTitleAndDateLatestFirst(): array
    {
        return $this->repository
            ->createQueryBuilder('p')
            ->select('p.createdAt', 'p.title')
            ->andWhere('p.enabled = true')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /** @inheritdoc */
    public function findOneBySlug(string $slug): Post
    {
        return $this->repository
            ->createQueryBuilder('p')
            ->andWhere('p.enabled = true')
            ->andWhere('p.url = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult()
            ;
    }

    /** @inheritdoc */
    public function findOneById(string $id): Post
    {
        return $this->repository
            ->createQueryBuilder('p')
            ->andWhere('p.enabled = true')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult()
            ;
    }

    /** @inheritdoc */
    public function findOneByIdAlsoDisabled(string $id): Post
    {
        return $this->repository
            ->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult()
            ;
    }



    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Post::POSTS_PER_PAGE);
        $paginator->setCurrentPage($page);
        return $paginator;
    }
}
