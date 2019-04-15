<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Friendship;

class AjaxController extends Controller
{
    /**
     * @Route("/ajax", name="ajax")
     */
    public function index()
    {
        return $this->render('ajax/index.html.twig', [
            'controller_name' => 'AjaxController',
        ]);
    }

    /**
     * @route("/ajaxListUser/{slug}", name="ajax_ajaxlistuser")
     */
    public function ajaxListUser(Request $request, $slug = null)
    {

        if ($slug == null) {
            $slug =
                'mine';
        }

        /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator  = $this->get('knp_paginator');

        $currentUserCity = $this->getuser()->getCurrentLocation()['city'];
        $em = $this->getDoctrine()->getManager();
        $fsRepo = $em->getRepository(Friendship::class);

        if ($slug == 'mine') {

            $allFriendshipsQuery = $fsRepo->createQueryBuilder('fs')
                ->where('fs.friend = :currentuser')
                ->addOrderBy('fs.status', 'ASC')
                ->setParameter('currentuser', $this->getUser()->getId())
                ->getQuery();

            $friendships = $paginator->paginate($allFriendshipsQuery, $request->query->getInt('page', 1), 60);

            return $this->render('home/ajaxListFriendships.html.twig', [
                'friendships' => $friendships,
                'filter' => $slug
            ]);
        } else {
            $userRepo = $em->getRepository(User::class);

            $myFriends = $fsRepo->createQueryBuilder('fs')
                ->select('IDENTITY(fs.friend)')
                ->where('fs.user = :currentuser')
                ->setParameter('currentuser', $this->getUser()->getId());
            dump($myFriends->getQuery()->getResult());

            if ($slug == 'local') {
                $allUsersQuery = $userRepo->createQueryBuilder('u')
                    ->where('u.currentLocation like :city')
                    ->andWhere('u.id != :currentuser')
                    ->andWhere($myFriends->expr()->notIn('u.id', $myFriends->getDQL()))
                    ->setParameter('city', '%' . $currentUserCity . '%')
                    ->setParameter('currentuser', $this->getUser()->getId())
                    ->getQuery();
            } elseif ($slug == 'global') {
                $allUsersQuery = $userRepo->createQueryBuilder('u')
                    ->where('u.id != :currentuser')
                    ->andWhere($myFriends->expr()->notIn('u.id', $myFriends->getDQL()))
                    ->setParameter('currentuser', $this->getUser()->getId())
                    ->getQuery();
            }

            $users = $paginator->paginate($allUsersQuery, $request->query->getInt('page', 1), 60);

            return $this->render('home/ajaxListUser.html.twig', [
                'users' => $users,
                'filter' => $slug
            ]);
        }
    }

    /**
     * @route("/ajaxListNotif", name="ajax_ajaxlistnotif")
     */
    public function ajaxListNotif(Request $request)
    {
        return $this->render('home/ajaxListNotif.html.twig');
    }

    /**
     * @route("/ajaxListNotifScroll/{offset}", name="ajax_ajaxlistnotifscroll")
     */
    public function ajaxListNotifScroll(Request $request, $offset = null)
    {
        $offset += 10;
        return $this->render('home/ajaxListNotifScroll.html.twig', ['offset' => $offset]);
    }

    /**
     * @route("/ajaxGetCounter", name="ajax_ajaxgetcounter")
     */
    public function ajaxGetCounter(Request $request)
    {
        return $this->render('home/ajaxCountNotif.html.twig');
    }
}
