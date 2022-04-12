<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Utils\HttpMethodSorter;
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
        $sortMethod = (new HttpMethodSorter($request))->getMethod();
        $latestPosts = $posts->findLatest($page, $sortMethod);
        return $this->render('blog/index.html.twig', [
            'title' => 'Blog index page',
            'paginator' => $latestPosts,
        ]);
    }
}