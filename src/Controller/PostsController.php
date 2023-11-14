<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;


class PostsController extends AbstractController
{
    // Constructor
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    // Set Route (All Posts Action)
    #[Route('/posts', name: 'all_posts')]
    public function showAllPostsAction(): Response
    {
        //Get data from DB using repository
        $repository = $this->em->getRepository(Post::class);

        // use to show data by getting from Entity Repositories by method findAll() = Select * from Posts
        $posts = $repository->findAll();

        // dump die function to print the repository data
        // dd($posts);

        //render a Twig template
        // app/index.html.twig
        // return $this->render('app/index.html.twig', [
        //     'posts' => $posts
        // ]);

        // ...

        // In your PostsController, before rendering the template, define and pass an empty commentForm
        $commentForm = $this->createForm(CommentType::class)->createView();
        return $this->render('app/index.html.twig', [
            'posts' => $posts,
            'commentForm' => $commentForm,
        ]);
    }


    // Search Action
    // get post by id
    #[Route('/posts/{id}', name: 'post')]
    public function showPostAction($id): Response
    {
        //Get data from DB using repository
        $repository = $this->em->getRepository(Post::class);

        // Assuming 'id' is the name of the field you want to search by
        $post = $repository->findOneBy(['id' => $id]);
        // dd($post);

        return $this->render('post/search.html.twig', [
            'posts' => $post ? [$post] : [], // Wrap $post in an array for consistent handling in the template
        ]);
    }



    // // Assuming you have a 'title' field in your Post entity
    // #[Route('/posts/title', name: 'post_by_title')]
    // public function showPostByTitleAction($title): Response
    // {
    //     // Get data from DB using repository
    //     $repository = $this->em->getRepository(Post::class);

    //     // Assuming 'title' is the name of the field you want to search by
    //     $post = $repository->findOneBy(['title' => $title]);

    //     return $this->render('post/search.html.twig', [
    //         'posts' => $post ? [$post] : [], // Wrap $post in an array for consistent handling in the template
    //     ]);
    // }


    #[Route('/posts/search/{keyword}', name: 'search_posts')]
    public function searchPostsAction($keyword): Response
    {
        // Get data from DB using repository
        $repository = $this->em->getRepository(Post::class);

        // Assuming 'title' and 'content' are fields you want to search
        $posts = $repository->createQueryBuilder('p')
            ->where('p.title LIKE :keyword OR p.description LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();

        return $this->render('post/search.html.twig', [
            'posts' => $posts,
        ]);
    }








    // Set Route (All Posts Action)
    #[Route('/admin/dashboard/allPosts', name: 'admin_board')]
    public function adminPosts(): Response
    {
        //Get data from DB using repository
        $repository = $this->em->getRepository(Post::class);

        $posts = $repository->findAll();

        return $this->render('admin/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    // Create New Post Action
    #[Route('/create', name: 'create_route')]
    public function createPostAction(Request $request)
    {
        // Create form to insert new Post
        $createPost = new Post;
        $form = $this->createFormBuilder($createPost)
            // set input fields names, types
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Create New Post', 'attr' => array('style' => 'margin-top:10px')))
            ->getForm();

        // Bind request with Form Data
        $form->handleRequest($request);

        // Handle request and validate
        // check if form is submitted and valid, then get input fields
        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form['title']->getData();
            $description = $form['description']->getData();

            // after getting input data, set the fields
            $createPost->setTitle($title);
            $createPost->setDescription($description);

            // use entity manager to persist and flush and then show a message on successful added
            $em = $this->em;
            $em->persist($createPost);
            $em->flush();

            $this->addFlash('message', 'Post Added Successfully');
            return $this->redirectToRoute('all_posts');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }



    // Related posts of a logged in user
    #[Route('/userPosts', name: 'user_posts')]
    public function userPostsAction()
    {
        // Check if the user is authenticated
        if (!$this->getUser()) {
            // Handle the case when the user is not logged in
            // You can redirect them to the login page or display a message.
            return $this->redirectToRoute('login'); // Adjust the route name.
        }

        $user = $this->getUser();
        // $relatedPosts = $user->getPosts();
        $relatedPosts = $this->em->getRepository(Post::class)->findBy(['user' => $user]);

        // dump($user);
        // dump($relatedPosts);

        return $this->render('post/userPosts.html.twig', [
            'posts' => $relatedPosts,
        ]);
    }



    // View Action
    #[Route('/view/{id}', name: 'view_route')]
    public function viewPostAction($id)
    {
        $repository = $this->em->getRepository(Post::class);

        $post = $repository->find($id);
        // dd($post);

        return $this->render('post/view.html.twig', [
            'post' => $post,
        ]);
    }





    // Update Action
    #[Route('/edit/{id}', name: 'edit_route')]
    public function editPostAction(Request $request, $id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        // dd($post);
        $post->setTitle($post->getTitle());
        $post->setDescription($post->getDescription());

        $updateForm = $this->createFormBuilder($post)
            ->add('title', TextType::class, ['label' => false])
            ->add('description', TextareaType::class, ['label' => false])
            ->add('save', SubmitType::class, ['label' => 'Save Changes', 'attr' => array('style' => 'margin-top:10px')])
            ->getForm();

        $updateForm->handleRequest($request);

        if ($updateForm->isSubmitted() && $updateForm->isValid()) {

            $title = $updateForm['title']->getData();
            $description = $updateForm['description']->getData();

            // after getting input data, set the fields
            $post->setTitle($title);
            $post->setDescription($description);

            $post = $this->em->getRepository(Post::class)->find($id);

            $em = $this->em;
            $em->persist($post);
            $em->flush();

            $this->addFlash('message', 'Post Updated Successfully');
            return $this->redirectToRoute('admin_board');
        }

        return $this->render('post/edit.html.twig', [
            'updateForm' => $updateForm->createView()
        ]);
    }


    // Delete Action
    #[Route('/delete/{id}', name: 'delete_route')]
    public function deletePostAction(Request $request, $id)
    {
        $em = $this->em;
        $post = $this->em->getRepository(Post::class)->find($id);
        // dd($post);

        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('admin_board');
    }


    // /**
    //  * @param Request $request
    //  * @return Response
    //  */
    // #[Route('/comment', name: 'comment')]
    // public function commentAction(Request $request): Response
    // {
    //     $comment = new Comment();
    //     $form = $this->createForm(CommentType::class, $comment);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($comment);
    //         $entityManager->flush();
    //         return $this->redirectToRoute('blog_index');
    //     }
    //     return $this->render('comment/index.html.twig', [
    //         'comment' => $comment,
    //         'form' => $form->createView(),
    //     ]);
    // }
    //

}
