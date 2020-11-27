<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em       = $em;
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('home/pages/index.html.twig', [
            'user' => $user,
            'pageNav' => 'home',
            'searchbar' => ['status' => false, 'value' => null]
        ]);
    }

}