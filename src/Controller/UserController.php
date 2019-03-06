<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use \App\Entity\User;
use \App\Form\ProfileType;

class UserController extends AbstractController
{
    /**
     * @Route("/myProfile", name="home_myprofile")
     */
    public function myProfile()
    {
        
        $form = $this->createForm(ProfileType::class, $this->getUser());

        return $this->render('user/myProfile.html.twig', [
            'controller_name'   => 'UserController',
            'formProfile'       => $form->createView(),
        ]);
    }
}
