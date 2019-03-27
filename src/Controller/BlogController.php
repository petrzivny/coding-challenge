<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\PostRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/blog") */
final class BlogController extends AbstractController
{
    /** @var PostRepositoryInterface */
    private $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Displays paginated list of Posts, ordered by date of creation from the latest to oldest.
     *
     * @Route("/{page<[1-9]\d*>}", defaults={"page": "1"}, methods={"GET"}, name="blog_index")
     */
    public function index(int $page): Response
    {
        $posts = $this->postRepository
            ->findAllPaginatedLatestFirst($page);

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * Displays details of single post.
     *
     * @Route("/post/{slug}", methods={"GET"}, name="post_show")
     * @param string $slug - url of post
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showPostDetail(string $slug): Response
    {
        $post = $this->postRepository
            ->findOneBySlug($slug);

        $post->increaseVisitCountByOne();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->render('blog/show.html.twig', [
            'post' => $post,
        ]);
    }
}
