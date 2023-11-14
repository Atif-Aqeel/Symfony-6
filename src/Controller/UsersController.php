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


class UsersController extends AbstractController
{
    // Constructor
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    // Set Route (All Posts Action)
    #[Route('/allUsers', name: 'all_users')]
    public function showAllUsers(): Response
    {
        //Get data from DB using repository
        $repository = $this->em->getRepository(User::class);

        $users = $repository->findAll();

        return $this->render('user/user.html.twig', [
            'users' => $users,
        ]);
    }


    // Create New User
    #[Route('/createUser', name: 'create_user')]
    public function createUserAction(Request $request)
    {
        // Create form to insert new Post
        $createUser = new User;
        $form = $this->createFormBuilder($createUser)
            // set input fields names, types
            ->add('email', TextType::class, array('attr' => array('class' => 'form-control')))
            // ->add('roles', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('password', TextareaType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Create New User', 'attr' => array('style' => 'margin-top:10px')))
            ->getForm();

        // Bind request with Form Data
        $form->handleRequest($request);

        // Handle request and validate
        // check if form is submitted and valid, then get input fields
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form['email']->getData();
            // $roles = $form['roles']->getData();
            $password = $form['password']->getData();

            // after getting input data, set the fields
            $createUser->setEmail($email);
            // $createUser->setRoles($roles);
            $createUser->setPassword($password);

            // use entity manager to persist and flush and then show a message on successful added
            $em = $this->em;
            $em->persist($createUser);
            $em->flush();

            $this->addFlash('message', 'User Added Successfully');
            return $this->redirectToRoute('all_users');
        }

        $this->addFlash('message', 'User Not Added, Try Again');
        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }



    // Update Action
    #[Route('/editUser/{id}', name: 'edit_user')]
    public function editUserAction(Request $request, $id)
    {
        $user = $this->em->getRepository(User::class)->find($id);
        // dd($post);
        $user->setEmail($user->getEmail());
        $user->setPassword($user->getPassword());

        $updateForm = $this->createFormBuilder($user)
            // ->add('roles', TextareaType::class, array('attr' => array('class' => 'form-control')))

            ->add('email', TextType::class, ['label' => false])
            // ->add('email', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('password', TextareaType::class, ['label' => false])
            // ->add('password', TextareaType::class, array('attr' => array('class' => 'form-control')))

            ->add('save', SubmitType::class, ['label' => 'Save Changes', 'attr' => array('style' => 'margin-top:10px')])
            ->getForm();

        $updateForm->handleRequest($request);

        if ($updateForm->isSubmitted() && $updateForm->isValid()) {

            $email = $updateForm['email']->getData();
            $password = $updateForm['password']->getData();

            // after getting input data, set the fields
            $user->setEmail($email);
            $user->setPassword($password);

            $user = $this->em->getRepository(User::class)->find($id);

            $em = $this->em;
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'User Updated Successfully');
            return $this->redirectToRoute('all_users');
        }

        // $this->addFlash('message', 'User Not Updated');
        return $this->render('user/edit.html.twig', [
            'updateForm' => $updateForm->createView()
        ]);
    }


    // View Action
    #[Route('/viewUser/{id}', name: 'view_user')]
    public function viewUserAction($id)
    {
        $repository = $this->em->getRepository(User::class);

        $user = $repository->find($id);
        // dd($user);

        return $this->render('user/view.html.twig', [
            'user' => $user,
        ]);
    }


    // Delete Action
    #[Route('/deleteUser/{id}', name: 'delete_User')]
    public function deleteUserAction(Request $request, $id)
    {
        $em = $this->em;
        $user = $this->em->getRepository(User::class)->find($id);
        // dd($user);

        $em->remove($user);
        $em->flush();

        return $this->render('user/delete.html.twig');

        // return $this->redirectToRoute('all_users');
    }




    // // Related posts of a logged in user
    // #[Route('/userPosts', name: 'user_posts')]
    // public function userPostsAction()
    // {
    //     // Check if the user is authenticated
    //     if (!$this->getUser()) {
    //         // Handle the case when the user is not logged in
    //         // You can redirect them to the login page or display a message.
    //         return $this->redirectToRoute('login'); // Adjust the route name.
    //     }

    //     $user = $this->getUser();
    //     // $relatedPosts = $user->getPosts();
    //     $relatedPosts = $this->em->getRepository(Post::class)->findBy(['user' => $user]);

    //     // dump($user);
    //     // dump($relatedPosts);

    //     return $this->render('post/userPosts.html.twig', [
    //         'posts' => $relatedPosts,
    //     ]);
    // }


    // // Search Action
    // // get post by id
    // #[Route('/posts/{id}', name: 'post')]
    // public function showPostAction($id): Response
    // {
    //     //Get data from DB using repository
    //     $repository = $this->em->getRepository(Post::class);

    //     // use to show data by getting from Entity Repositories by method findAll() = Select * from Posts
    //     // $post = $repository->find($id);

    //     // Assuming 'id' is the name of the field you want to search by
    //     $post = $repository->findOneBy(['id' => $id]);

    //     // dd($post);

    //     return $this->render('post/search.html.twig', [
    //         'posts' => $post ? [$post] : [], // Wrap $post in an array for consistent handling in the template
    //     ]);
    // }

}
