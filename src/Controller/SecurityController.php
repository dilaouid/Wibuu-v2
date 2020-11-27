<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Repository\SideImgRepository;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils, SideImgRepository $repository, String $flash = '')
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $lastUsername = null;
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $flash = 'warning';
            $lastUsername = $authenticationUtils->getLastUsername();
            $this->addFlash(
                'warning',
                'Les identifiants saisis sont incorrects'
            );
        }
        $pageInfos = [
            'sideImg'       => $repository->getRandom(),
            'sideImg_pos'   => 'right',
            'pagename'      => 'Connexion',
            'title'         => 'Se connecter',
            'icon'          => 'fas fa-camera-retro',
            'btn'           => 'Connexion',
            'link'          => [
                ['txt' => 'Mot de passe oublié ?', 'href' => '/forgot_password'],
                ['txt' => 'Pas encore inscrit ?', 'href' => '/register']
            ]
        ];
        return $this->render("login/base.html.twig", [
            'page' => 'login',
            'flashtype' => $flash,
            'lastusername' => $lastUsername,
            'data' => $pageInfos
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(SideImgRepository $repository)
    {
        $pageInfos = [
            'sideImg'       => $repository->getRandom(),
            'sideImg_pos'   => 'right',
            'pagename'      => 'Connexion',
            'title'         => 'Se connecter',
            'icon'          => 'fas fa-camera-retro',
            'btn'           => 'Connexion',
            'link'          => [
                ['txt' => 'Mot de passe oublié ?', 'href' => '/forgot_password'],
                ['txt' => 'Pas encore inscrit ?', 'href' => '/register']
            ]
        ];
        return $this->render("login/base.html.twig", [
            'page' => 'login',
            'fail' => ["status" => true, "label" => '', "msg" => ''],
            'flashtype' => '',
            'lastusername' => '',
            'data' => $pageInfos
        ]);
    }
}
