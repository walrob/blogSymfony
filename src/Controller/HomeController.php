<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'text' => 'Hello BLOGUERS',
            'posts' => $posts,
        ]);
    }
}
