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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


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
     * @Route("/user/post/mylist/{page}",
     *     name="post.mylist",
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
    public function mylist(EntityManagerInterface $em, PaginatorInterface $paginator, TokenStorageInterface $tokenStorage, int $page) : Response
    {
        $user = $tokenStorage->getToken()->getUser();

        $posts = $paginator->paginate(
            $em->getRepository(Post::class)->createQueryBuilder('j')
            ->andWhere('j.author = :val')
            ->setParameter('val', $user),
            $page,
            $this->getParameter('number_items'),
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'j.publishedAt',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'DESC',
            ]
        );

        return $this->render('post/mylist.html.twig', [
            'posts' => $posts,
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

            return $this->redirectToRoute('post.mylist');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
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
     * Edit post.
     *
     * @Route("/post/{id}/edit", name="post.edit", methods="GET|POST", requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Post $post
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $em, Post $post, TokenStorageInterface $tokenStorage) : Response
    {
        $user = $tokenStorage->getToken()->getUser();

        //controla que el usuario es el autor del post
        if (! ($user === $post->getAuthor())) {
            throw new AccessDeniedException('Unable to access this page!');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('post.mylist');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete post.
     *
     * @Route("/post/{id}/delete", name="post.delete", methods="DELETE", requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Post $post
     *
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em, Post $post, TokenStorageInterface $tokenStorage) : Response
    {
        $user = $tokenStorage->getToken()->getUser();

        if (! ($user === $post->getAuthor())) {
            throw new AccessDeniedException('Unable to access this page!');
        }

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post.mylist');
    }
}