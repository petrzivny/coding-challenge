<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use Pagerfanta\Pagerfanta;

interface PostRepositoryInterface
{

    /**
     * Query for paginated list of posts. Only enabled posts.
     *
     * @param int $page - number of current page to display
     * @return Pagerfanta|Post[] - actually, return is not Post[], it is here just to help IDE with auto-completion
     */
    public function findAllPaginatedLatestFirst(int $page): Pagerfanta;

    /**
     * Query for not paginated list of posts. Include both enabled and disabled posts.
     *
     * @return Post[]
     */
    public function findAllLatestFirstAlsoDisabled(): array;

    /**
     * Query for not paginated list of posts. Include disabled posts. Only certain columns for REST API list.
     *
     * @return Post[]
     */
    public function findAllSelectOnlyTitleAndDateLatestFirst(): array;

    /**
     * Query for single post matched slug/url. Post must be enabled.
     *
     * @param string $slug
     * @return Post
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlug(string $slug): Post;

    /**
     * Query for single post matched id. Post must be enabled.
     *
     * @param string $id
     * @return Post
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById(string $id): Post;

    /**
     * Query for single post matched id. Post can be both enabled and disabled.
     *
     * @param string $id
     * @return Post
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdAlsoDisabled(string $id): Post;
}
