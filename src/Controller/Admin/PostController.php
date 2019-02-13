<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\PostType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class PostController extends AbstractController
{
    /**
     * Lists all posts entities.
     *
     * @Route("/admin/post/list/{page}",
     *     name="admin.post.list",
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

        return $this->render('admin/post/list.html.twig', [
            'posts' => $posts,
        ]);
    }
    
    /**
     * Create post.
     *
     * @Route("/admin/post/create", name="admin.post.create", methods="GET|POST")
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

            return $this->redirectToRoute('admin.post.list');
        }

        return $this->render('admin/post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Show post.
     *
     * @Route("/admin/post/{id}", name="admin.post.show", methods="GET|POST")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Post $post
     *
     * @return Response
     */
    public function show(Request $request, EntityManagerInterface $em, Post $post) : Response
    {
        return $this->render('admin/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * Edit post.
     *
     * @Route("/admin/post/{id}/edit", name="admin.post.edit", methods="GET|POST", requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Post $post
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $em, Post $post) : Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin.post.list');
        }

        return $this->render('admin/post/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete post.
     *
     * @Route("/admin/post/{id}/delete", name="admin.post.delete", methods="DELETE", requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Post $post
     *
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $em, Post $post) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('admin.post.list');
    }

    /**
     * Delete comment.
     *
     * @Route("/admin/comment/{id}/delete", name="admin.comment.delete", methods="DELETE", requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param Comment $comment
     *
     * @return Response
     */
    public function deleteComment(Request $request, EntityManagerInterface $em, Comment $comment) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $em->remove($comment);
            $em->flush();
        }

        return $this->render('admin/post/show.html.twig', [
            'post' => $comment->getPost(),
        ]);
    }
}
