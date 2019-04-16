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

    /**
     * @route("/ajaxSearch/{query}", name="ajax_ajaxsearch")
     */
    public function ajaxSearch(Request $request, $query = null)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);
        
        $queryData = explode(' ', $query);
        $result = array();
        $result['byName'] = array();
        $result['byLastname'] = array();
        $result['byUsername'] = array();
        $result['bestResults'] = array();

        foreach ($queryData as $value) {           
            $byName = $userRepo->createQueryBuilder('u')
                                ->where('u.name LIKE :query ')
                                ->setParameter('query', $value.'%')
                                ->setMaxResults(10)
                                ->getQuery()
                                ->getResult();
            if(!in_array($byName, $result)) {
                $result['byName'] = $byName;
            }
            if(in_array($byName, $result['byLastname']) || in_array($byName, $result['byUsername'])) {
                $result['bestResults'] = $byName;
                unset($result['byLastname']);
                unset($result['byName']);
                unset($result['byUsername']);
            }

            $byLastname = $userRepo->createQueryBuilder('u')
                                    ->where('u.lastname LIKE :query')
                                    ->setParameter('query', $value.'%')
                                    ->setMaxResults(10)
                                    ->getQuery()
                                    ->getResult();
            if(!in_array($byLastname, $result['byName']) && !in_array($byLastname, $result['byUsername'])) {
                $result['byLastname'] = $byLastname;
            }
            if(in_array($byLastname, $result['byName']) || in_array($byLastname, $result['byUsername'])) {
                $result['bestResults'] = $byLastname;
                unset($result['byLastname']);
                unset($result['byName']);
                unset($result['byUsername']);
            }

            $byUsername = $userRepo->createQueryBuilder('u')
                                    ->where('u.username LIKE :query')
                                    ->setParameter('query', $value.'%')
                                    ->setMaxResults(10)
                                    ->getQuery()
                                    ->getResult();
            if(!in_array($byUsername, $result['byLastname']) && !in_array($byUsername, $result['byName'])) {
                $result['byUsername'] = $byUsername;
            }
            if(in_array($byUsername, $result['byLastname']) || in_array($byUsername, $result['byName'])) {
                $result['bestResults'] = $byLastname;
                unset($result['byLastname']);
                unset($result['byName']);
                unset($result['byUsername']);
            }
        }

        $result['byAll'] = array();
        if(empty($result['byName']) && empty($result['byLastname']) && empty($result['byUsername'])) {
            foreach ($queryData as $value) {            
                $byAll = $userRepo->createQueryBuilder('u')
                    ->where('u.name LIKE :query OR u.lastname LIKE :query OR u.username LIKE :query')
                    ->setParameter('query', $value.'%')
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getResult();
                if(!in_array($byAll, $result)) {
                    $result['byAll'] = $byAll;
                }
            }
        }
        dump($result);
        return $this->render('ajax/ajaxListSearch.html.twig', ['result' => $result, 
                                                                'query' => $query]
        );
    }
}
