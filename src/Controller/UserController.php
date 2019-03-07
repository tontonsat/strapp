<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use \App\Entity\User;
use \App\Entity\Media;
use \App\Form\ProfileType;
use \App\Form\PasswdType;
use \App\Form\UploadType;

class UserController extends AbstractController
{
    /**
     * @Route("/myProfile", name="home_myprofile")
     */
    public function myProfile(UserRepository $repo, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {

        $user = $repo->findOneBy(['id' => $this->getUser()->getId()]);

        if(!$user->getMedia()) {
            $media = new Media();
        }
        else {
            $media = $user->getmedia();
        }
        
        $formInfo = $this->createForm(ProfileType::class, $user);
        $formPassword = $this->createForm(PasswdType::class, $user);
        $formImage = $this->createForm(UploadType::class, $media);

        $formImage->handleRequest($request);
        if($formImage->isSubmitted() && $formImage->isValid()) {
            $user->setMedia($media);
            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash('notice-profile','profile picture updated with success!');
            return $this->redirectToRoute("home_myprofile");
        }

        $formInfo->handleRequest($request);
        if($formInfo->isSubmitted() && $formInfo->isValid()) {
            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash('notice-profile','Personnal information edited with success');
            return $this->redirectToRoute("home_myprofile");
        }

        $formPassword->handleRequest($request);
        if($formPassword->isSubmitted() && $formPassword->isValid()) {
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            $manager->persist($user);
            $manager->flush();
            
            $this->addFlash('notice-profile','Password modified with success!');
            return $this->redirectToRoute("home_myprofile");
        }
        return $this->render('user/myProfile.html.twig', [
            'controller_name'   => 'UserController',
            'formProfile'       => $formInfo->createView(),
            'formPassword'       => $formPassword->createView(),
            'formImage'       => $formImage->createView(),
        ]);
    }
}
