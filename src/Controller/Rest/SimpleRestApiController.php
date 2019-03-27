<?php declare(strict_types=1);

namespace App\Controller\Rest;

use App\Repository\PostRepositoryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/no-fosrest")
 */
final class SimpleRestApiController extends AbstractController
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
     * @Route("/posts", methods={"GET"}, name="simple_api_all_posts")
     * @throws NotFoundHttpException
     */
    public function posts(): JsonResponse
    {
        $posts = $this->postRepository
            ->findAllSelectOnlyTitleAndDateLatestFirst();

        return (new JsonResponse($posts))->setEncodingOptions(JSON_PRETTY_PRINT);
    }

    /**
     * Returns a detail of a single blog post in JSON.
     *
     * @Route("/post/{id}", methods={"GET"}, name="simple_api_post_detail")
     * @throws NotFoundHttpException
     */
    public function post(string $id): JsonResponse
    {
        try {
            $post = $this->postRepository
                ->findOneById($id);
        } catch (NoResultException | NonUniqueResultException $e) {
            throw new NotFoundHttpException('No post with this ID.');
            // TODO probably log real exceptions
        }

        $post->increaseVisitCountByOne();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return (new JsonResponse($post))->setEncodingOptions(JSON_PRETTY_PRINT);
    }
}
