<?php

namespace App\Controller;

use App\Entity\SideImg;
use App\Repository\SideImgRepository;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class SideImgController extends AbstractController
{
    /**
     * @var SideImgRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(SideImgRepository $repository, EntityManagerInterface $em)
    {
        $this->repository   = $repository;
        $this->em           = $em;
    }

    public function index(): Response
    {
        
    }
}