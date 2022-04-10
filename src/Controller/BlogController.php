<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function index(Request $request, int $page, PostRepository $posts): Response
    {
        $sortMethod = $request->get("method");
        $latestPosts = $posts->findLatest($page, empty($sortMethod) ? "DESC" : $sortMethod);
        return $this->render('blog/index.html.twig', [
            'title' => 'Blog index page',
            'paginator' => $latestPosts,
        ]);
    }
}