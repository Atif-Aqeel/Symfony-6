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


    // 
    #[Route('/posts/add_comment/{id}', name: 'add_comment')]
    // #[Route('/comment/{postId}', name: 'comment_create')]

    public function addComment(Request $request, $id)
    {
        // $post = $this->getDoctrine()->getRepository(Post::class)->find($postId);
        $post = $this->em->getRepository(Post::class)->find($id);
        $user = $this->getUser(); // Get the current user

        $comment = new Comment();
        $comment->setPost($post);
        // $comment->setUser($user);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $entityManager = $this->getDoctrine()->getManager();
            $entityManager = $this->em;
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app/index.html.twig');
        }

        // return $this->render('app/index.html.twig', [
        //     'posts' => $post,
        //     'comments' => $post->getComments(),
        //     'form' => $form->createView(),
        // ]);

        return $this->render('comment/create.html.twig', [
            'form' => $form->createView()
        ]);
        // return $this->redirectToRoute('all_posts');

    }
}
