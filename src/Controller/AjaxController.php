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

class AjaxController extends AbstractController
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
     * @Route("/like/{id}", name="like_post")
     * @param Request $request
     * @param \App\Entity\Post $post
     * @param \App\Repository\LikeRepository $likeRepository
     * @param \App\Repository\NotificationRepository $notifRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function likePost(Post $post, LikeRepository $likeRepository, NotificationRepository $notifRepository) : Response
    {
        $user = $this->getUser();
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);
        if (!$post) return $this->json(['code' => 404, 'message' => 'Post inexistant'], 404);
        $authorRepository   = $this->getDoctrine()->getRepository(User::Class);
        $author             = $authorRepository->findOneBy(["id" => $post->getAuthor()]);
        if ($author->getPrivate() && !$author->isFollowedBy($user->getId())) return $this->json([
                'code'      => 400,
                'message'   => 'Action impossible'
            ], 400);
        if ($post->isLikedByUser($user)) {
            $like = $likeRepository->findOneBy([
                'post'      => $post,
                'author'    => $user
            ]);
            $this->em->remove($like);
        } else {
            $like = new Like();
            $like->setPost($post)
                 ->setAuthor($user);
            $this->em->persist($like);
        }
        $this->em->flush();
        return $this->json(['code' => 200, 'message' => 'OK'], 200);
    }

    /**
     * @Route("/bookmark/{id}", name="bookmark_post")
     * @param Request $request
     * @param \App\Entity\Post $post
     * @param \App\Repository\BookmarkRepository $bookmarkRepository
     * @param \App\Repository\NotificationRepository $notifRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bookmarkPost(Post $post, BookmarkRepository $bookmarkRepository, NotificationRepository $notifRepository) : Response
    {
        $user = $this->getUser();
        if (!$user) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);
        if (!$post) return $this->json(['code' => 404, 'message' => 'Post inexistant'], 404);
        $authorRepository   = $this->getDoctrine()->getRepository(User::Class);
        $author             = $authorRepository->findOneBy(["id" => $post->getAuthor()]);
        if ($author->getPrivate() && !$author->isFollowedBy($user->getId())) return $this->json([
                'code'      => 400,
                'message'   => 'Action impossible'
            ], 400);
    
        if ($post->isBookmarkedByUser($user)) {
            $bookmark = $bookmarkRepository->findOneBy([
                'post'    => $post,
                'user'    => $user
            ]);
            $this->em->remove($bookmark);
        } else {
            $bookmark = new Bookmark();
            $bookmark->setPost($post)
                     ->setUser($user);
            $this->em->persist($bookmark);
        }
        $this->em->flush();
        return $this->json(['code' => 200, 'message' => 'OK'], 200);
    }

    /**
     * @Route("/get/post/{userid}/{offset}", name="get_post_ajax")
     * @param Request $request
     * @param \App\Repository\PostRepository $postRepository
     * @param \App\Repository\NotificationRepository $notifRepository
     * @param \App\Repository\UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPost(PostRepository $postRepository, UserRepository $userRepository, NotificationRepository $notifRepository, $offset, $userid) : Response
    {
        $user = $this->getUser();
        if (!$user || !is_numeric($offset) || !is_numeric($userid)) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);
        $private = $user->getId() != $userid;
        $author = $userRepository->findOneBy(['id' => $userid]);
        if (!$author || ($author->getPrivate() && !$author->isFollowedBy($user->getId()))) return $this->json([
            'code'      => 400,
            'message'   => 'Action impossible'
        ], 400);
        if ($private) {
            $post = $postRepository->findBy(['author' => $userid, 'private' => false], ['id' => 'DESC'], 6, $offset);
        } else {
            $post = $postRepository->findBy(['author' => $userid], ['id' => 'DESC'], 6, $offset);
        }
        if (!$post) return $this->json(['code' => 404, 'message' => 'Aucun résultat'], 404);
        $JSONpost = Array();
        for ($i = 0; $i < count($post); $i++) {
            $JSONpost[$i]['uuid']       = $post[$i]->getUuid();
            $JSONpost[$i]['like']       = (int)$post[$i]->getLikenb();
            $JSONpost[$i]['comment']    = (int)$post[$i]->getCommentnb();
            $JSONpost[$i]['private']    = $post[$i]->getPrivate();
            $JSONpost[$i]['liked']      = $post[$i]->isLikedByUser($user);
            $JSONpost[$i]['commented']  = $post[$i]->isCommentedByUser($user);
            $JSONpost[$i]['url']        = $post[$i]->getPass() ? "background: url(/post/".$post[$i]->getUuid()."/".$post[$i]->getPass()."/view) center / cover no-repeat;" : "background: url(/post/".$post[$i]->getUuid()."/view) center / cover no-repeat;";
        }
        return $this->json(['code' => 200, 'data' => $JSONpost, 'message' => 'OK'], 200);
    }

    /**
     * @Route("/follow/{uuid}", name="follow_user")
     * @param Request $request
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\NotificationRepository $notifRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function askFollow(UserRepository $userRepository, NotificationRepository $notifRepository, $uuid) : Response
    {
        $user       = $this->getUser();
        $useruuid   = $user->getUuid();
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        if (!$user ||$useruuid == $uuid) return $this->json([
            'code'      => 403,
            'message'   => 'Unauthorized'
        ], 403);
        $toFollow = $userRepository->findOneBy(['uuid' => $uuid]);
        if (!$toFollow) return $this->json([
            'code'      => 404,
            'message'   => "Cet utilisateur n'existe pas"
        ], 404);
        $privateUser = $toFollow->getPrivate();

        if (in_array($useruuid, $toFollow->getAwaitFollow())) { // Si l'utilisateur est déjà dans la awaitfollow list
            $toFollow->setAwaitFollow(array_diff($toFollow->getAwaitFollow(), [$useruuid]));
            $status = 'unawait';
            $notifications = $notifRepository->findOneBy(['author' => $user, 'dest' => $toFollow, 'nature' => 'askfollow']);
            if ($notifications) {
                $this->em->remove($notifications);
            }
        } elseif ($privateUser && !$toFollow->isFollowedBy($useruuid)) { // si l'utilisateur est privé et ne suit pas encore la cible
            $getAwaitFollow = $toFollow->getAwaitFollow();
            $remove = $getAwaitFollow;
            $add    = $getAwaitFollow;
            array_diff($remove, [$useruuid]);
            array_push($add, $useruuid);
            in_array($useruuid, $getAwaitFollow) ? $toFollow->setAwaitFollow($remove) : $toFollow->setAwaitFollow($add);
            $status = 'await';
            $notifications = new Notification();
            $notifications->setAuthor($user)
                            ->setDest($toFollow)
                            ->setMoment($date)
                            ->setNature('askfollow');
            $this->em->persist($notifications);
        } elseif ($toFollow->isFollowedBy($useruuid)) { // si l'utilisateur suit la cible
            $toFollow->setFollowers(array_diff($toFollow->getFollowers(), [$useruuid]));
            $user->setFollowing(array_diff($user->getFollowing(), [$toFollow->getUuid()]));
            $user->setFollowing_nb($user->getFollowing_nb() - 1);
            $toFollow->setFollowers_nb($toFollow->getFollowers_nb() - 1);
            $status = 'unfollowed';
            $notifications = $notifRepository->findOneBy(['author' => $user, 'dest' => $toFollow, 'nature' => 'follow']);
            if ($notifications) {
                $this->em->remove($notifications);
            }
        } else { // sinon un simple follow :)
            $toFollowGetFollowers   = $toFollow->getFollowers();
            $userGetFollowing       = $user->getFollowing();
            array_push($toFollowGetFollowers, $useruuid);
            array_push($userGetFollowing, $toFollow->getUuid());
            $toFollow->setFollowers($toFollowGetFollowers);
            $user->setFollowing($userGetFollowing);
            $user->setFollowing_nb($user->getFollowing_nb() + 1);
            $toFollow->setFollowers_nb($toFollow->getFollowers_nb() + 1);
            $status = 'done';
            $notifications = new Notification();
            $notifications->setAuthor($user)
                        ->setDest($toFollow)
                        ->setMoment($date)
                        ->setNature('follow');
            $this->em->persist($notifications);
        }
        $this->em->persist($toFollow);
        $this->em->persist($user);
        $this->em->flush();
        return $this->json(['code' => 200, 'statut' => $status, 'message' => 'OK'], 200);
    }
    
    /**
     * @Route("/clear_notification/{id}", name="remove_notification")
     * @param Request $request
     * @param \App\Entity\Notification $notification
     * @param \App\Repository\NotificationRepository $notifRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeNotif(Notification $notification, NotificationRepository $notifRepository) : Response {
        $user = $this->getUser();
        if (!$notification || $notification->getDest() != $user) {
            return $this->json(['code' => 500, 'message' => 'Action impossible'], 500);
        }
        $this->em->remove($notification);
        $this->em->flush();
        return $this->json(['code' => 200, 'message' => 'OK'], 200);
    }
}
