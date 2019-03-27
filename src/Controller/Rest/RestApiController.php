<?php declare(strict_types=1);

namespace App\Controller\Rest;

use App\Repository\PostRepositoryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestApiController extends AbstractFOSRestController
{
    /** @var PostRepositoryInterface */
    private $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Returns a full list of blog posts without textual content and tags in JSON.
     *
     * @Rest\Get("/posts")
     *
     */
    public function posts(): View
    {
        $posts = $this->postRepository
            ->findAllSelectOnlyTitleAndDateLatestFirst();

        return View::create($posts, JsonResponse::HTTP_OK);
    }


    /**
     * Returns a detail of a single blog post in JSON.
     *
     * @Rest\Get("/post/{id}")
     * @throws NotFoundHttpException
     */
    public function post(string $id): View
    {
        try {
            $post = $this->postRepository
                ->findOneById($id);
        } catch (NoResultException | NonUniqueResultException $e) {
            throw new NotFoundHttpException('No post with this ID.');
            // TODO probably log real exceptions
        }

        return View::create($post, JsonResponse::HTTP_OK);
    }
}
