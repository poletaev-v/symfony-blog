<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Event\CommentCreatedEvent;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Utils\HttpMethodSorter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

    public function show(string $slug, PostRepository $posts): Response
    {
        $post = $posts->findOneBy(["slug" => $slug]);

        if (is_null($post)) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(CommentType::class);
        return $this->render('blog/detail.html.twig', [
            'title' => 'Post detail page',
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    public function commentNew(Request $request, PostRepository $posts, EventDispatcherInterface $eventDispatcher, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $slug = array_filter(explode("/", $request->getPathInfo()), fn($partUrl) => !in_array($partUrl, ["comment", "new", null]));
        $post = $posts->findOneBy(["slug" => $slug]);
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $post->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
            $eventDispatcher->dispatch(new CommentCreatedEvent($comment));

            return $this->redirectToRoute('blog_detail', ['slug' => $post->getSlug()]);
        }

        return $this->render('blog/comment_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}