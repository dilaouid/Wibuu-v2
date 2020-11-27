<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Bookmark;
use App\Entity\Post;

use App\Repository\PostRepository;
use App\Repository\UserRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Doctrine\ORM\EntityManagerInterface;

class GalleryController extends AbstractController
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
     * @Route("/user/{username}", name="view_profile")
     * @param Request $request
     * @param \App\Repository\PostRepository $postRepository
     * @param \App\Repository\UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profile(string $username = null, PostRepository $postRepository, UserRepository $userRepository): Response
    {
        $user           = $this->getUser();
        $bookmarksObj = null;
        if ($username == $user->getUsername()) { // S'il s'agit du profil de l'utilisateur
            $profil =         [ 'uuid'          => $user->getUuid(),
                                'id'            => $user->getId(),
                                'username'      => $username,
                                'firstname'     => $user->getFirstname(),
                                'lastname'      => $user->getFirstname(),
                                'followers_nb'  => is_null($user->getFollowers_nb()) ? 0 : $user->getFollowers_nb(),
                                'following_nb'  => is_null($user->getFollowing_nb()) ? 0 : $user->getFollowing_nb(),
                                'description'   => $user->getDescription(),
                                'publications'  => $user->getPublications(),
                                'locked'        => false,
                                'me'            => true,
                                'mutual'        => null ];
            $profilID = $user->getId();
            $bookmarksObj = $user->getBookmarks();
            $identifications = $user->getIdentificationPosts();
        } else { // S'il s'agit du profil d'un utilisateur tiers
            $profil             = $userRepository->findOneBy(['username' => $username]);
            if (!$profil) { // Si le profil recherché n'existe pas, on est redirigé
                return $this->redirectToRoute('home');
            }
            $profil->me = false;
            // On remplace les ID par les usernames dans le tableau des followers
            if ($profil->getFollowers() != null) {
                $profil->mutual = array_intersect($profil->getFollowers(), $user->getFollowers());
                for ($i = 0; $i < count($profil->mutual); $i++) {
                    $profil->mutual[$i] = $userRepository->findOneBy(['uuid' => $profil->mutual[$i]])->getUsername();
                }
            } else {
                $profil->mutual = null;
            }
            $profil->locked     = $profil->getPrivate() && !$profil->isFollowedBy($user->getId());
            $profilID           = $profil->getId();
            $identifications    = $profil->getIdentificationPosts();
        }
        $posts = $postRepository->findBy(['author' => $profilID], ['id' => 'DESC'], 9);
        return $this->render('profile/base.html.twig', [
            'posts'     => $posts,
            'bookmarks' => $bookmarksObj,
            'tagged'    => $identifications,
            'user'      => $user,
            'profile'   => $profil,
            'pageNav'   => 'profile',
            'searchbar' => ['status' => false, 'value' => null]
        ]);
    }

    /**
     * @Route("/post/{uuid}/view", name="print_publication")
     * @param Request $request
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\PostRepository $postRepository
     */
    public function viewPublication(string $uuid = null, PostRepository $postRepository, UserRepository $userRepository)
    {
        $post = $postRepository->findOneBy(['uuid' => $uuid]);
        if (!$post || $post->getPrivate() == true) {
            $this->headerImage('phil', null, 'img/');
        }
        $author = $post->getAuthor();
        $author->getPrivate() && !$author->isFollowedBy($this->getUser()->getId()) ? $this->headerImage('phil', null, 'img/') : $this->headerImage($post->getUuid(), $author->getUuid());
    }

    /**
     * @Route("/post/{uuid}/{keylock}/view", name="print_private_publication")
     * @param Request $request
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\PostRepository $postRepository
     */
    public function viewPrivatePublication(string $uuid = null, string $keylock, PostRepository $postRepository, UserRepository $userRepository)
    {
        $post = $postRepository->findOneBy(['uuid' => $uuid]);
        if (!$post || $post->getPass() != $keylock) {
            $this->headerImage('phil', null, 'img/');
        }
        $author = $post->getAuthor();
        $author->getPrivate() && !$author->isFollowedBy($this->getUser()->getId()) ? $this->headerImage('phil', null, 'img/') : $this->headerImage($post->getUuid(), $author->getUuid());
    }

    private function headerImage(string $imagename, $uuid, string $path = 'img/publications/')
    {
        $uuid       != null ? $path .= $uuid . '/' : '';
        $fullpath   = 'assets/' . $path . $imagename . '.jpeg';
        $fp         = fopen($fullpath, 'rb');

        header("Content-Type: image/jpeg");
        header("Content-Length: " . filesize($fullpath));
        fpassthru($fp);

        exit();
    }

}