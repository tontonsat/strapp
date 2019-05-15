<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Vote;
use App\Entity\Friendship;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\VoteRepository;

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
            if(strlen($value) >= 3) {
                $byName = $userRepo->createQueryBuilder('u')
                    ->where('u.name LIKE :query ')
                    ->setParameter('query', '%' . $value . '%')
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getResult();
                $result['byName'] = $byName;
                if (empty($result['bestResult'])) {
                    $result['bestResult'] = $byName;
                } else {
                    $result['bestResult'] = array_merge($result['bestResult'], $byName);
                }

                $byLastname = $userRepo->createQueryBuilder('u')
                    ->where('u.lastname LIKE :query')
                    ->setParameter('query', '%' . $value . '%')
                    ->setMaxResults(6)
                    ->getQuery()
                    ->getResult();

                $result['byLastname'] = array_udiff($byLastname, $result['byName'], $result['byUsername'], function ($res1, $res2) {
                    return $res1->getId() - $res2->getId();
                });
                if (empty($result['bestResult'])) {
                    $result['bestResult'] = $byLastname;
                } else {
                    $result['bestResult'] = array_merge($result['bestResult'], $byLastname);
                }

                $byUsername = $userRepo->createQueryBuilder('u')
                    ->where('u.username LIKE :query')
                    ->setParameter('query', '%' . $value . '%')
                    ->setMaxResults(6)
                    ->getQuery()
                    ->getResult();
                $result['byUsername'] = array_udiff($byUsername, $result['byName'], $result['byLastname'], function ($res1, $res2) {
                    return $res1->getId() - $res2->getId();
                });
                if (empty($result['bestResult'])) {
                    $result['bestResult'] = $byUsername;
                } else {
                    $result['bestResult'] = array_merge($result['bestResult'], $byUsername);
                }
                $result['bestResult'] = array_merge($result['bestResult'], $byUsername);
            }
        }

        $result['byAll'] = array();
            foreach ($queryData as $value) {            
            $byAll = $userRepo->createQueryBuilder('u')
                ->where('u.name LIKE :query AND u.username LIKE :query')
                ->setParameter('query', '%' .$value.'%')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
            $result['byAll'] = array_unique($byAll);
        }

        $result['bestResult'] = array_unique(array_uintersect($result['bestResult'], $result['byAll'], function ($res1, $res2) {
            return spl_object_hash($res1) <=> spl_object_hash($res2);
        }));

        return $this->render('ajax/ajaxListSearch.html.twig', ['result' => $result, 
                                                                'query' => $query]
        );
    }

    /**
     * @route("/ajaxlistUserScroll/{slug}/{offset}", name="ajax_ajaxlistuserscroll")
     */
    public function ajaxListUserScroll(Request $request, $slug = null, $offset = null)
    {
        $currentUserCity = $this->getuser()->getCurrentLocation()['city'];
        $em = $this->getDoctrine()->getManager();
        $fsRepo = $em->getRepository(Friendship::class);

        if ($slug == 'mine') {

            $allFriendships = $fsRepo->createQueryBuilder('fs')
                ->where('fs.friend = :currentuser')
                ->addOrderBy('fs.status', 'ASC')
                ->setParameter('currentuser', $this->getUser()->getId())
                ->setFirstResult($offset)
                ->setMaxResults(48)
                ->getQuery()
                ->getResult();
            $offset += 48;
            return $this->render('ajax/ajaxListUserScroll.html.twig', [
                'friendships' => $allFriendships,
                'offset' => $offset
            ]);
        } else {
            $userRepo = $em->getRepository(User::class);

            $myFriends = $fsRepo->createQueryBuilder('fs')
                ->select('IDENTITY(fs.friend)')
                ->where('fs.user = :currentuser')
                ->setParameter('currentuser', $this->getUser()->getId());

            if ($slug == 'local') {
                $allUsers = $userRepo->createQueryBuilder('u')
                    ->where('u.currentLocation like :city')
                    ->andWhere('u.id != :currentuser')
                    ->andWhere($myFriends->expr()->notIn('u.id', $myFriends->getDQL()))
                    ->setParameter('city', '%' . $currentUserCity . '%')
                    ->setParameter('currentuser', $this->getUser()->getId())
                    ->orderBy('u.id', 'DESC')
                    ->setFirstResult($offset)
                    ->setMaxResults(48)
                    ->getQuery()
                    ->getResult();
            } elseif ($slug == 'global') {
                $allUsers = $userRepo->createQueryBuilder('u')
                    ->where('u.id != :currentuser')
                    ->andWhere($myFriends->expr()->notIn('u.id', $myFriends->getDQL()))
                    ->setParameter('currentuser', $this->getUser()->getId())
                    ->orderBy('u.id', 'DESC')
                    ->setFirstResult($offset)
                    ->setMaxResults(48)
                    ->getQuery()
                    ->getResult();
            }

            $offset += 48;

            return $this->render('ajax/ajaxListUserScroll.html.twig', [
                'users' => $allUsers,
                'offset' => $offset
            ]);
        }
    }
    /**
     * @route("/ajaxListVotesScroll/{offset}", name="ajax_ajaxlistvotesscroll")
     */
    public function ajaxListVotesScroll(VoteRepository $voteRepo , $offset = null)
    {
        $em = $this->getDoctrine()->getManager();
        $fsRepo = $em->getRepository(Friendship::class);

        $allFriends = $fsRepo->createQueryBuilder('fs')
        ->select('partial fs.{id, user}')
        ->leftJoin('fs.user', 'user')
        ->addSelect('user')
        ->where('fs.friend = :currentuser AND fs.status = 1')
        ->setParameter('currentuser', $this->getUser()->getId())
        ->getQuery()
        ->getResult();
        if(!empty($allFriends)) {
            foreach($allFriends as $friend) {
                $friendsId[] = $friend->getUser()->getId();
            }
            $votes = $voteRepo->findByUserIdAndFriendsOffset($this->getUser()->getId(), $friendsId,$offset);
        }
        else {
            $votes = [];
        }

        $now = new \Datetime('now');
        $voteData = [];
        foreach($votes as $vote) {
            if($vote->getStatus() == 1 && $vote->getDateEnd() <= $now) {
                $vote->setStatus(0);
                $voteData[] = $vote;
            }
            else {
                $voteData[] = $vote;
            }
        }
        $em->flush();

        $offset += 10;

        return $this->render('ajax/ajaxListVotesScroll.html.twig', [
            'votes' => $voteData,
            'offset' => $offset
        ]);
    }

    /**
     * @route("/ajaxGetVotePins", name="ajax_getvotepins")
     */
    public function ajaxGetVotePins()
    {    
        $em = $this->getDoctrine()->getManager();
        $voteRepo = $em->getRepository(Vote::class);
        $data = $em->createQuery('SELECT v FROM App\Entity\Vote v')->getArrayResult();

        return $this->json($data);
    }

    /**
     * @route("/ajaxGetUserPos", name="ajax_getcurrentuserpos")
     */
    public function ajaxGetUserPos()
    {    
        return new JsonResponse($this->getUser()->getCurrentLocation());
    }

    /**
     * @route("/ajaxGetPinView/{slug}", name="ajax_getpinview")
     */
    public function ajaxGetPinView($slug = null)
    {    
        $em = $this->getDoctrine()->getManager();
        $voteRepo = $em->getRepository(Vote::class);

        $voteData = $voteRepo->createQueryBuilder('v')
            ->where('v.id = :id')
            ->setParameter('id', $slug)
            ->getQuery()
            ->getSingleResult();
        return $this->render('ajax/ajaxPinView.html.twig', ['vote' => $voteData]
        );
    }
    
    /**
     * @route("/ajaxComment", name="ajax_comment")
     */
    public function ajaxComment()
    {    
        $comments = [];

        return $this->render('ajax/ajaxComments.html.twig', ['comments' => $comments, 
                                                            'commentForm' => $formComment->createView()]
        );
    }
}