<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use App\Entity\User;
use App\Repository\UserRepository;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home_root")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
        ]);
    }

    /**
     * @Route("/home", name="home_home")
     */
    public function home(ObjectManager $manager, Request $request)
    {
        
        $updateCoord = $this->createFormBuilder($this->getUser())
        ->add('submit', SubmitType::class,['label' => 'Update location'])
        ->add('coord', HiddenType::class, ['mapped' => false, 'data' => ''])->getForm();

        $updateCoord->handleRequest($request);

        if($updateCoord->isSubmitted() && $updateCoord->isValid()) {

            $coord = $request->request->get('form')['coord'];

            $this->getuser()->setCurrentLocation($coord);
            $manager->flush();

            $updatedCoord = $this->getUser()->getCurrentLocation();
            $this->addFlash('notice-coord','Current position updated with success!'.' ['. $updatedCoord['city'] .', '. $updatedCoord['country'] .', ['. $updatedCoord['coord'] .']]');
            return $this->redirectToRoute("home_home");
        }

        return $this->render('home/home.html.twig', [
            'updateCoord' => $updateCoord->createView(),
        ]);
    }

    /**
     * @route("/listUser/{slug}", name="home_listuser")
     */
    public function listUser(Request $request, $slug = NULL) {

        if($slug == NULL) {
            $slug = 'local';
        }

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(User::class);

        $currentUserCity = $this->getuser()->getCurrentLocation()['city'];
        if($slug == 'local') {
            $allUsersQuery = $userRepo->createQueryBuilder('u')
                ->where('u.currentLocation like :city' )
                ->andWhere('u.id != :currentuser' )
                ->setParameter('city', '%'.$currentUserCity.'%')
                ->setParameter('currentuser', $this->getUser()->getId())
                ->getQuery();
        }
        elseif($slug == 'global') {
            $allUsersQuery = $userRepo->createQueryBuilder('u')->getQuery();
        }

        /* @var $paginator \Knp\Component\Pager\Paginator */
        $paginator  = $this->get('knp_paginator');

        $users = $paginator->paginate($allUsersQuery, $request->query->getInt('page', 1), 60);
        dump($users);
        return $this->render('home/listUser.html.twig', [
            'users' => $users,
            'filter' => $slug
        ]);
    }
}
