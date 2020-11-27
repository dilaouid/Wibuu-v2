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

class PostController extends AbstractController
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
     * @Route("/post/{uuid}", name="view_post")
     * @param Request $request
     * @param \App\Entity\Post $post
     * @param \App\Repository\PostRepository $postRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewPost(Post $post, PostRepository $postRepository, string $uuid): Response
    {
        $user           = $this->getUser();
        $post           = $postRepository->findOneBy(['uuid' => $uuid, 'private' => 0]);
        if (!$post) return $this->redirectToRoute('home');
        $author         = $post->getAuthor();
        if ($author->getPrivate() && !$author->isFollowedBy($user->getUuid())) return $this->redirectToRoute('home');
        return $this->render('post/base.html.twig', [
            'post'      => $post,
            'author'    => $author,
            'pageNav'   => 'explorer',
            'searchbar' => ['status' => true, 'value' => null]
        ]);
    }
}