<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class AppController extends AbstractController
{
    #[Route('/homepage', name: 'app_homepage')]
    public function index()
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute("admin_dashboard");
        } else {
            return $this->redirectToRoute('all_posts');
            // return $this->redirectToRoute('app_login');

            // return $this->render('security/login.html.twig');
        }

        //     return $this->render('admin/index.html.twig', [
        //         'user' => $this->getUser()
        //     ]);
    }
}
