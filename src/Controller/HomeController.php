<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use App\Entity\User;
use App\Entity\Friendship;
use App\Repository\UserRepository;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home_root")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', []);
    }

    /**
     * @Route("/home", name="home_home")
     */
    public function home(ObjectManager $manager, Request $request)
    {
        return $this->render('home/home.html.twig');
    }

    /**
     * @route("/listUser/{slug}", name="home_listuser")
     */
    public function listUser(Request $request, $slug = null)
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

            return $this->render('home/listFriendships.html.twig', [
                'friendships' => $friendships,
                'filter' => $slug
            ]);
        } else {
            $userRepo = $em->getRepository(User::class);

            $myFriends = $fsRepo->createQueryBuilder('fs')
                ->select('IDENTITY(fs.friend)')
                ->where('fs.user = :currentuser')
                ->setParameter('currentuser', $this->getUser()->getId());

            if ($slug == 'local') {
                $allUsersQuery = $userRepo->createQueryBuilder('u')
                    ->where('u.currentLocation like :city')
                    ->andWhere('u.id != :currentuser')
                    ->andWhere($myFriends->expr()->notIn('u.id', $myFriends->getDQL()))
                    ->setParameter('city', '%' . $currentUserCity . '%')
                    ->setParameter('currentuser', $this->getUser()->getId())
                    ->orderBy('u.id', 'DESC')
                    ->getQuery();
            } elseif ($slug == 'global') {
                $allUsersQuery = $userRepo->createQueryBuilder('u')
                    ->where('u.id != :currentuser')
                    ->andWhere($myFriends->expr()->notIn('u.id', $myFriends->getDQL()))
                    ->setParameter('currentuser', $this->getUser()->getId())
                    ->orderBy('u.id', 'DESC')
                    ->getQuery();
            }

            $users = $paginator->paginate($allUsersQuery, $request->query->getInt('page', 1), 60);

            return $this->render('home/listUser.html.twig', [
                'users' => $users,
                'filter' => $slug
            ]);
        }
    }

    /**
     * @route("/ajaxListUser/{slug}", name="home_ajaxlistuser")
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
     * @route("/ajaxListNotif", name="home_ajaxlistnotif")
     */
    public function ajaxListNotif(Request $request)
    {
        return $this->render('home/ajaxListNotif.html.twig');
    }

    /**
     * @route("/ajaxGetCounter", name="home_ajaxgetcounter")
     */
    public function ajaxGetCounter(Request $request)
    {
        return $this->render('home/ajaxCountNotif.html.twig');
    }
}
