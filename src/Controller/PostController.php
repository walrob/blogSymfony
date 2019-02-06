<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\PostType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class PostController extends AbstractController
{
    /**
     * @Route("/user/post", name="post")
     */
    public function index()
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    /**
     * @Route("/user/post/list/{page}",
     *     name="post.list",
     *     methods="GET",
     *     defaults={"page": 1},
     *     requirements={"page" = "\d+"}
     * )
     *
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @param int $page
     *
     * @return Response
     */
    public function list(EntityManagerInterface $em, PaginatorInterface $paginator, int $page) : Response
    {
        $posts = $paginator->paginate(
            $em->getRepository(Post::class)->createQueryBuilder('j'),
            $page,
            $this->getParameter('number_items'),
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'j.publishedAt',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'DESC',
            ]
        );

        return $this->render('post/list.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show post.
     *
     * @Route("/user/post/{id}", name="post.show", methods="GET|POST")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Post $post
     *
     * @return Response
     */
    public function show(Request $request, EntityManagerInterface $em, Post $post, TokenStorageInterface $tokenStorage) : Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $tokenStorage->getToken()->getUser();
            $comment->setUser($em->merge($user));

            $comment->setPost($em->merge($post));
            
            $em->persist($comment);
            $em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Create post.
     *
     * @Route("/user/post/create", name="post.create", methods="GET|POST")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em, TokenStorageInterface $tokenStorage) : Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $tokenStorage->getToken()->getUser();
            $post->setAuthor($em->merge($user));

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post.list');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
