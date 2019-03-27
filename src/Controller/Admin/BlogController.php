<?php declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/blog")
 * @isGranted("ROLE_ADMIN")
 */
final class BlogController extends AbstractController
{
    /** @var PostRepositoryInterface */
    private $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Admin blog overview.
     *
     * @Route(methods={"GET"}, name="admin_blog_index")
     */
    public function index()
    {
        $posts = $this->postRepository->findAllLatestFirstAlsoDisabled();

        return $this->render('blog/admin/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * Displays details of single post with admin details.
     *
     * @Route("/post/show/{id}", methods={"GET"}, name="admin_post_show")
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showPostDetail(string $id): Response
    {
        $post = $this->postRepository->findOneByIdAlsoDisabled($id);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $post->increaseVisitCountByOne();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->render('blog/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * Create new post.
     *
     * @Route("/post/new", methods={"GET", "POST"}, name="new_post")
     * @throws \Exception - new \DateTimeImmutable() and when title is not set before url generation
     */
    public function newPost(Request $request): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUrl($post->generateUrl());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('blog/admin/new.html.twig', [ 'form' => $form->createView() ]);
    }

    /**
     * Edit existing post.
     *
     * @Route("/post/edit/{id}", methods={"GET", "POST"}, name="edit_post")
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function editPost(string $id, Request $request): Response
    {
        $post = $this->postRepository->findOneByIdAlsoDisabled($id);

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('blog/admin/edit.html.twig', [ 'form' => $form->createView() ]);
    }
}
