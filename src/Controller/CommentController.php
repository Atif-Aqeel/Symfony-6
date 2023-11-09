<?php

// src/Controller/CommentController.php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    // Constructor
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/add_comment/{id}', name: 'add_comment')]
    public function addComment(Request $request, $id): Response
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $user = $this->getUser(); // Get the current user

        $comment = new Comment();
        $comment->setPost($post);
        $comment->setCommentUser($user); // Set the user for the comment

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->em->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment added successfully');
        }

        return $this->render('comment/index.html.twig', [
            'post' => $post,
            'comments' => $post->getComments(),
            'form' => $form->createView(),
        ]);

        // return $this->redirectToRoute('all_posts');
    }
}
