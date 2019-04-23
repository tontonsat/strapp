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
use App\Repository\VoteRepository;
use App\Form\VoteType;
use App\Entity\Vote;

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
     * @Route("/vote/{vote}", name="home_vote")
     */
    public function vote(VoteRepository $repo, ObjectManager $manager, Request $request, Vote $vote = null)
    {
        if(is_null($vote)) {
            $vote = new Vote();
        }
        else {
            $vote = $repo->findOneBy(['author' => $this->getUser()->getId()]);
        }

        $formVote = $this->createForm(VoteType::class, $vote);

        $formVote->handleRequest($request);
        if ( $formVote->isSubmitted() && $formVote->isValid()) {
            $coord = $request->request->get('vote')['coord'];
            $duration = $request->request->get('vote')['duration'];
            $vote->setCoord($coord)
                ->setStatus(1)
                ->setAuthor($this->getUser())
                ->setDateCreate(new \Datetime('now'));
            $vote->setDateEnd(new \Datetime('+'. $duration .' hour'));

            $manager->persist($vote);
            $manager->flush();

            $this->addFlash('notice-vote-submit', 'Story submitted with success!');
            return $this->redirectToRoute("home_home");
        }

        return $this->render('vote/vote.html.twig', ['formVote' => $formVote->createView()]);
    }

    /**
     * @route("/listUser/{slug}", name="home_listuser")
     */
    public function listUser(Request $request, $slug = null)
    {

        if ($slug == null) {
            $slug = 'mine';
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
}
