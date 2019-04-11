<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use App\Repository\UserRepository;
use App\Repository\FriendshipRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use \App\Entity\User;
use \App\Entity\Media;
use \App\Form\ProfileType;
use \App\Form\PasswdType;
use \App\Form\UploadType;
use \App\Form\MoodType;
use \App\Form\BioType;
use Symfony\Component\HttpFoundation\Response;
use \App\Entity\Friendship;

class UserController extends Controller
{
    /**
     * @Route("/myProfile", name="home_myprofile")
     */
    public function myProfile(UserRepository $repo, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {

        $user = $repo->findOneBy(['id' => $this->getUser()->getId()]);

        if ($user->getMedia() == null) {
            $media = new Media();
        } else {
            $media = $user->getmedia();
        }

        $updateCoord = $this->createFormBuilder($this->getUser())
            ->add('coord', HiddenType::class, ['mapped' => false, 'data' => ''])->getForm();

        $formInfo = $this->createForm(ProfileType::class, $user);
        $formPassword = $this->createForm(PasswdType::class, $user);
        $formMood = $this->createForm(MoodType::class, $user);
        $formBio = $this->createForm(BioType::class, $user);
        $formImage = $this->createForm(UploadType::class, $media);

        $formImage->handleRequest($request);
        $formInfo->handleRequest($request);
        $formPassword->handleRequest($request);
        $formMood->handleRequest($request);
        $formBio->handleRequest($request);
        $updateCoord->handleRequest($request);

        if ($updateCoord->isSubmitted() && $updateCoord->isValid()) {

            $coord = $request->request->get('form')['coord'];

            $this->getuser()->setCurrentLocation($coord);
            $manager->flush();

            $updatedCoord = $this->getUser()->getCurrentLocation();
            $this->addFlash('notice-coord', '');
            return $this->redirectToRoute("home_myprofile");
        } elseif ($formBio->isSubmitted() && $formBio->isValid()) {
            $manager->flush();

            $this->addFlash('notice-profile', 'Bio updated with success!');
            return $this->redirectToRoute("home_myprofile");
        } elseif ($formImage->isSubmitted() && $formImage->isValid()) {

            $user->setMedia($media);
            $user->getMedia()->setWebPath('uploads/avatars/' . $media->getImageName());
            $manager->flush();

            $this->addFlash('notice-profile', 'Profile picture updated with success!');
            return $this->redirectToRoute("home_myprofile");
        } elseif ($formInfo->isSubmitted() && $formInfo->isValid()) {
            $manager->flush();

            $this->addFlash('notice-profile', 'Personnal information edited with success!');
            return $this->redirectToRoute("home_myprofile");
        } elseif ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $manager->flush();

            $this->addFlash('notice-profile', 'Password modified with success!');
            return $this->redirectToRoute("home_myprofile");
        } elseif ($formMood->isSubmitted() && $formMood->isValid()) {
            $manager->flush();
            $this->addFlash('notice-mood', '');
            return $this->redirectToRoute("home_myprofile");
        }
        return $this->render('user/myProfile.html.twig', [
            'controller_name'   => 'UserController',
            'formProfile'       => $formInfo->createView(),
            'formPassword'       => $formPassword->createView(),
            'formImage'       => $formImage->createView(),
            'formMood'       => $formMood->createView(),
            'updateCoord'       => $updateCoord->createView(),
            'formBio'       => $formBio->createView(),
        ]);
    }
    /**
     * @Route("/user/{slug}", name="home_user")
     */
    public function profile(UserRepository $repo, Request $request, ObjectManager $manager, $slug = null)
    {
        if ($slug == null) {
            return $this->redirectToRoute("home_userList");
        }
        if ($slug == $this->getUser()->getId()) {
            return $this->redirectToRoute('home_myprofile');
        }
        $user = $repo->findOneBy(['id' => $slug]);

        $em = $this->getDoctrine()->getManager();
        $fsRepo = $em->getRepository(Friendship::class);

        if ($friendship = $fsRepo->createQueryBuilder('fs')
            ->where('fs.user = :currentuser AND fs.friend = :friend')
            ->setParameter('currentuser', $this->getUser()->getId())
            ->setParameter('friend', $user->getId())
            ->getQuery()->getResult()
        ) {
            $isFriend = true;
        } else {
            $isFriend = false;
        }
        /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator  = $this->get('knp_paginator');

        $allFriendsQuery = $fsRepo->createQueryBuilder('fs')
            ->where('fs.user = :user AND fs.status = 1')
            ->setParameter('user', $user->getId())
            ->getQuery();

        $friends = $paginator->paginate($allFriendsQuery, $request->query->getInt('page', 1), 12);

        return $this->render('user/profile.html.twig', [
            'user'   => $user,
            'isFriend' => $isFriend,
            'friends' => $friends,
            'friendship' => $friendship
        ]);
    }

    /**
     * @Route("/addFriend/{slug}", name="home_addFriend")
     */
    public function ajaxAddFriend(UserRepository $repo, Request $request, ObjectManager $manager, $slug = null): Response
    {
        if ($slug == null) {
            return $this->redirectToRoute("home_userList");
        }
        $friend = $repo->findOneBy(['id' => $slug]);

        $friendship1 = $this->getUser()->addFriend($friend);
        $friendship2 = $friend->addFriend($this->getUser());

        $friendship1->setOrigin($this->getUser()->getId());
        $friendship2->setOrigin($this->getUser()->getId());

        $managerNotif = $this->get('mgilet.notification');
        $url = $this->generateUrl('home_user', array('slug' => $this->getUser()->getId()));

        $notif = $managerNotif->createNotification($this->getUser()->getName() . ' ' . $this->getUser()->getLastname());
        $notif->setMessage('sent you a friend request.');
        $notif->setLink($url);

        $manager->persist($friendship1);
        $manager->persist($friendship2);

        $managerNotif->addNotification([$friend], $notif, true);

        $manager->flush();

        return $this->render('user/ajaxAddFriend.html.twig');
    }

    /**
     * @Route("/treatFriendRequest/{slug}/{state}", name="home_treatfriendrequest")
     */
    public function ajaxTreatFriendRequest(FriendshipRepository $repo, Request $request, ObjectManager $manager, $slug = null, $state = null): Response
    {
        if ($slug == null || $state == null) {
            return $this->redirectToRoute("home_userList");
        }

        $friendship1 = $repo->findOneBy(['id' => $slug]);

        $friendship2 = $repo->findOneBy(['user' => $friendship1->getFriend()->getId()]);


        if ($state == 1) {
            $date = new \Datetime();
            $friendship1->setStatus(1)->setDate($date);
            $friendship2->setStatus(1)->setDate($date);

            $managerNotif = $this->get('mgilet.notification');
            $url = $this->generateUrl('home_user', array('slug' => $this->getUser()->getId()));

            $notif = $managerNotif->createNotification($this->getUser()->getName() . ' ' . $this->getUser()->getLastname());
            $notif->setMessage('accepted your friend request.');
            $notif->setLink($url);

            $managerNotif->addNotification([$friendship1->getFriend()], $notif, true);
        } elseif ($state == 0) {
            $manager->remove($friendship1);
            $manager->remove($friendship2);
        }

        $manager->flush();

        return $this->render('user/ajaxTreatFriendRequest.html.twig', ['state' => $state]);
    }

    /**
     * @Route("/updateCoord/{slug}", name="home_updatecoord")
     */
    public function ajaxUpdateCoord(Request $request, ObjectManager $manager, $slug = null): Response
    {
        if ($slug == null) {
            return $this->redirectToRoute("home_root");
        }

        $this->getuser()->setCurrentLocation($slug);
        $manager->flush();

        return $this->render('user/ajaxUpdateCoord.html.twig');
    }
}
