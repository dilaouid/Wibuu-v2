<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Bookmark;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\Notification;

use App\Repository\PostRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Repository\BookmarkRepository;
use App\Repository\LikeRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Doctrine\ORM\EntityManagerInterface;

class NotificationController extends AbstractController
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
     * @Route("/notifications", name="notifications")
     * @param Request $request
     * @param \App\Repository\NotificationRepository $notifRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notificationsPage(NotificationRepository $notifRepository) : Response
    {
        $notifications = $notifRepository->findBy(['dest' => $this->getUser()]);
        if ($notifications) {
            for ($i=0; $i < count($notifications); $i++) $notifications[$i]->setOpened(true);
            $this->em->flush();
        }
        return $this->render('home/pages/notifications.html.twig', [
            'pageNav'   => 'notifications',
            'searchbar' => ['status' => false, 'value' => null]
        ]);
    }
}