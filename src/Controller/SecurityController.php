<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use \App\Entity\User;
use \App\Form\RegistrationType;

class SecurityController extends AbstractController
{
    /**
     * [register description]
     * @Route("/register", name="security_register")
     * @param  Request       $request
     * @param  ObjectManager $manager
     * @return [type]                 [description]
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, LoggerInterface $logger) {

        if(!$this->getUser()) {
            $user = new User();
        }
        else {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded)
                ->setDateSignup(new \Datetime())
                ->setMood('hello there')
                ->setAvatar('https://via.placeholder.com/150')
                ->setRatingWriter(0)
                ->setRatingReader(0);

            $logger->info("User register ok: ". $user->getUsername());

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('notice','Inscription ok!');
            return $this->redirectToRoute("home_root");
        }

        return $this->render('security/register.html.twig',[
            'controller_name'   => 'SecurityController',
            'user'              => $user,
            'formUser'          => $form->createView()
        ]);
    }

    /**
     * [login description]
     * @return [type] [description]
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils) : Response {
        
        // get the login error if there is one
        $errors = $authenticationUtils->getLastAuthenticationError();
        return $this->render('home/index.html.twig',[
                            'errors' => $errors
        ]);
    }

    /**
     * [logout description]
     * @return [type] [description]
     * @Route("/logout", name="security_logout")
     */
    public function logout() {}
}
