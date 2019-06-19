<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Friendship;
use App\Repository\VoteRepository;
use App\Repository\FriendshipRepository;
use App\Repository\RatingRepository;
use App\Form\VoteType;
use App\Entity\Vote;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Rating;

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
    public function home(VoteRepository $voteRepo)
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
            $votes = $voteRepo->findByUserIdAndFriends([$this->getUser()->getId()],['friends' => $friendsId]);
        }
        else {
            $votes = $voteRepo->findBy(['author' => $this->getUser()->getId()]);
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

        return $this->render('home/home.html.twig', ['votes' => $voteData]);
    }

    /**
     * @Route("/displayMap", name="home_displaymap")
     */
    public function displayMap()
    {
        return $this->render('home/map.html.twig');
    }

    /**
     * @Route("/displaynotifs", name="home_displaynotifs")
     */
    public function displayNotifs()
    {
        return $this->render('home/displayNotifs.html.twig');
    }

    /**
     * @Route("/vote/{vote}", name="home_vote")
     */
    public function vote(VoteRepository $repo, FriendshipRepository $fsRepo, ObjectManager $manager, Request $request, Vote $vote = null)
    {
        if(is_null($vote)) {
            $vote = new Vote();
        }
        else {
            $vote = $repo->findOneBy(['author' => $this->getUser()->getId()]);
        }

        $formVote = $this->createForm(VoteType::class, $vote);
        $managerNotif = $this->get('mgilet.notification');

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

            $url = $this->generateUrl('home_displayvote', ['vote' => $vote->getId()]);
            $myfriends = $fsRepo->findMyValidFriends($this->getUser()->getId());
            $notif = $managerNotif->createNotification($this->getUser()->getName() . ' ' . $this->getUser()->getLastname());
            $notif->setMessage('posted a new story!');
            $notif->setLink($url);

            foreach($myfriends as $friendship) {
                $managerNotif->addNotification([$friendship->getFriend()], $notif, true);
            }

            $this->addFlash('notice-vote-submit', 'Story submitted with success!');
            return $this->redirectToRoute("home_home");
        }

        return $this->render('vote/vote.html.twig', ['formVote' => $formVote->createView()]);
    }

    /**
     * @Route("/displayVote/{vote}", name="home_displayvote")
     */
    public function displayVote(Vote $vote = null, Request $request, ObjectManager $manager)
    {
        $em = $this->getDoctrine()->getManager();
        $voteRepo = $em->getRepository(Vote::class);
        $ratingRepo = $manager->getRepository(Rating::class);

        $vote = $voteRepo->findOneBy(['id' => $vote]);
        if($vote->getStatus() == 1 && $vote->getDateEnd() <= new \Datetime('now')) {
            $vote->setStatus(0);
            $em->flush();
        }
        
        $hasVoted = $ratingRepo->findOneBy(['author' => $this->getUser()->getId(), 'target' => $vote->getId()]);
        if($hasVoted != null && $vote->getStatus() == 0) {
            $managerNotif = $this->get('mgilet.notification');       
            $url = $this->generateUrl('home_displayvote', ['vote' => $vote->getId()]); 
            if($hasVoted->getValue() == $vote->getResult()) {    
                $notif = $managerNotif->createNotification('You were right!');
                $notif->setMessage(' (a story ended, see the result)');
                $notif->setLink($url);
                $this->getuser()->addMmr();
            }
            if($hasVoted->getValue() != $vote->getResult()) {   
                $notif = $managerNotif->createNotification('You were wrong...');
                $notif->setMessage(' (a story ended, see the result)');
                $notif->setLink($url);
                $this->getuser()->removeMmr();
            }
            $managerNotif->addNotification([$this->getUser()], $notif, true);
            $manager->remove($hasVoted);
            $manager->flush();
        }

        if(!is_null($hasVoted)) {
            $canVote = $hasVoted;
        }
        else {
            $canVote = null;
        }
        
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);

        $formComment->handleRequest($request);
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setDateCreate(new \Datetime('now'));
            $vote->addComment($comment);
            $manager->persist($comment);
            $manager->flush();
            
            return $this->redirectToRoute('home_displayvote', ['vote' => $vote->getId()]);
        }

        return $this->render('vote/displayVote.html.twig', ['vote' => $vote, 'commentForm' => $formComment->createView(), 'canVote' => $canVote]);
    }

    /**
     * @Route("/rateVote/{vote}/{slug}", name="home_ratevote")
     */
    public function rateVote(Vote $vote, $slug = null, Request $request, ObjectManager $manager)
    {
        $voteRepo = $manager->getRepository(Vote::class);
        $ratingRepo = $manager->getRepository(Rating::class);

        $vote = $voteRepo->findOneBy(['id' => $vote]);
        $hasVoted = $ratingRepo->findOneBy(['target' => $vote->getId(), 'author' => $this->getUser()->getId()]);
        
        if($vote->getStatus() == 1 && $vote->getDateEnd() <= new \Datetime('now')) {
            $vote->setStatus(0);
            $manager->flush();
            
            $this->addFlash('notice-vote-submit', 'Votes for this story have ended!');
            return $this->redirectToRoute('home_displayvote', ['vote' => $vote->getId()]);
        }

        $rating = new Rating();
        $rating->setAuthor($this->getUser())
                ->setDateCreate(new \Datetime('now'))
                ->setTarget($vote)
                ->setValue($slug);

        if(empty($hasVoted)) {
            $manager->persist($rating);
            $vote->addRating($rating);
            $this->getUser()->addRating($rating);
            $manager->flush();
    
            $this->addFlash('notice-vote-submit', 'Vote submitted with success!');
        }
        if(!empty($hasVoted)) {
            if($hasVoted->getValue() == $rating->getValue()) {
                $this->addFlash('notice-vote-submit', 'Vote already registered');
            }
            else {
                $rating = $hasVoted;
                $rating->setValue($slug);
                $manager->flush();
        
                $this->addFlash('notice-vote-submit', 'Vote submitted with success!');
            }
        }

        return $this->redirectToRoute('home_displayvote', ['vote' => $vote->getId()]);
    }

    /**
     * @Route("/deleteVote/{vote}", name="home_deletevote")
     */
    public function deleteVote(Vote $vote = null, ObjectManager $manager)
    {
        $voteRepo = $manager->getRepository(Vote::class);

        $vote = $voteRepo->findOneBy(['id' => $vote]);
        if($this->getUser()->getId() == $vote->getAuthor()->getId()) {
            $manager->remove($vote);
            foreach($vote->getComments() as $comment) {
                $vote->removeComment($comment);
            }
            foreach($vote->getRatings() as $rating) {
                $vote->removeRating($rating);
            }
            $manager->flush();
            $this->addFlash('notice-vote-submit', 'Story deleted with success!');
        }
        return $this->redirectToRoute('home_home');
    }

    /**
     * @route("/listUser/{slug}", name="home_listuser")
     */
    public function listUser(Request $request, $slug = null)
    {

        if ($slug == null) {
            $slug = 'mine';
        }

        $currentUserCity = $this->getuser()->getCurrentLocation()['city'];
        $em = $this->getDoctrine()->getManager();
        $fsRepo = $em->getRepository(Friendship::class);

        if ($slug == 'mine') {

            $allFriendships = $fsRepo->createQueryBuilder('fs')
                ->where('fs.friend = :currentuser')
                ->addOrderBy('fs.status', 'ASC')
                ->setParameter('currentuser', $this->getUser()->getId())
                ->setMaxResults(48)
                ->getQuery()
                ->getResult();

            return $this->render('home/listFriendships.html.twig', [
                'friendships' => $allFriendships,
                'filter' => $slug
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
                    ->setMaxResults(48)
                    ->getQuery()
                    ->getResult();
            } elseif ($slug == 'global') {
                $allUsers = $userRepo->createQueryBuilder('u')
                    ->where('u.id != :currentuser')
                    ->andWhere($myFriends->expr()->notIn('u.id', $myFriends->getDQL()))
                    ->setParameter('currentuser', $this->getUser()->getId())
                    ->orderBy('u.id', 'DESC')
                    ->setMaxResults(48)
                    ->getQuery()
                    ->getResult();
            }

            return $this->render('home/listUser.html.twig', [
                'users' => $allUsers,
                'filter' => $slug
            ]);
        }
    }

    /**
     * @route("/search/{query}", name="home_search")
     */
    public function search(Request $request, $query = null)
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
                    ->setMaxResults(100)
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
                    ->setMaxResults(100)
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
                    ->setMaxResults(100)
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

        return $this->render('home/listSearch.html.twig', ['result' => $result]
        );
    }
}
