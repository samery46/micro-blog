<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;


#[Route('/', requirements: ['_locale' => 'en|id'])]
class PostController extends AbstractController
{
    #[Route('/{_locale}', methods: ['GET'], name: 'posts.index')]
    public function index(Request $request, ManagerRegistry $doctrine, string $_locale = 'en'): Response
    {
        // $entityManager = $doctrine->getManager();
        // $user = $entityManager->getRepository(User::class)->find(1);
        // $entityManager->remove($user);
        // $entityManager->flush();
        // return new Response($user->getUser()->getEmail());
        // $entityManager = $doctrine->getManager();
        // $post = $entityManager->getRepository(Post::class)->find(1);
        // return new Response($post->getUser()->getEmail());
        // $user = $this->getUser();
        // return new Response($user->getPosts()[0]->getTitle());
        $posts = $doctrine->getRepository(Post::class)->findAllPosts($request->query->getInt('page', 1));
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/{_locale}/post/new', methods: ['GET', 'POST'], name: 'posts.new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $post = new Post();
        $post->setTitle('Write a blog post');
        $post->setContent('Post content');
        $post->setUser($this->getUser());
        $post->setCreatedAt(new \DateTimeImmutable('now'));
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            // $post = $form->getData();

            // ... perform some action, such as saving the task to the database
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('posts.index');
        }
        return $this->render('post/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{_locale}/post/{id}', methods: ['GET'], name: 'posts.show')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/{_locale}/post/{id}/delete', methods: ['POST'], name: 'posts.delete')]
    public function delete(Post $post, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $entityManager = $doctrine->getManager();
        $entityManager->remove($post);
        $entityManager->flush();
        return $this->redirectToRoute('posts.index');
    }

    #[Route('/{_locale}/post/{id}/edit', methods: ['GET', 'POST'], name: 'posts.edit')]
    public function edit(Post $post, Request $request, ManagerRegistry $doctrine): Response
    {
        // return $this->redirectToRoute('posts.index');
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $post = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            // ... perform some action, such as saving the task to the database
            return $this->redirectToRoute('posts.index');
        }
        return $this->render('post/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{_locale}/posts/user/{id}', methods: ['GET'], name: 'posts.user')]
    public function user(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        // return new Response($id);
        $posts = $doctrine->getRepository(Post::class)->findAllUserPosts($request->query->getInt('page', 1), $id);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    #[Route('/{_locale}/toggleFollow/{user}', methods: ['GET'], name: 'toggleFollow')]
    public function toggleFollow($user): Response
    {
        // return new Response($user);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return new Response('logic for toggling like/dislike functionality');
    }


    // #[Route('/post', name: 'app_post')]
    // public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/PostController.php',
    //     ]);
    // }
}
