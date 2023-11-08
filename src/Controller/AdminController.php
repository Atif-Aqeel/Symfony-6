<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[isGranted('ROLE_ADMIN', statusCode: 423)]

class AdminController extends AbstractController
{

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard()
    {
        // Checking to see if a User is Logged In
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED');


        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        // render to route admin_board
        return $this->redirectToRoute('admin_board');

        // return $this->render('admin/index.html.twig');
    }
}
