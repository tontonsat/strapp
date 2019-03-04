<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/myProfile", name="myprofile")
     */
    public function myProfile()
    {
        
        $form = $this->createForm(ProfileType::class, $app->getUser());

        return $this->render('user/myProfile.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
