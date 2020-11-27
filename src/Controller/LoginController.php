<?php

namespace App\Controller;

use App\Entity\SideImg;
use App\Entity\User;

use App\Form\UserType;

use App\Repository\SideImgRepository;
use App\Repository\UserRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use Doctrine\ORM\EntityManagerInterface;

class LoginController extends AbstractController
{
    
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $sideImg;
    private $pageInfos = array();

    public function __construct(SideImgRepository $repository, EntityManagerInterface $em)
    {
        $this->sideImg  = $repository->getRandom();
        $this->em       = $em;
        $this->pageInfos = [
            'sideImg'       => $this->sideImg
        ];
    }

    /**
     * @Route("/register/{key}", name="validation")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validate_account(Request $request, UserRepository $user, string $key)
    {
        $filesystem = new Filesystem();
        $validation = $user->findOneBy(['validation_key' => $key, 'activated' => false]);
        if ($validation) {
            $validation->setValidationKey('')
                ->setActivated(true);
            $this->em->persist($validation);
            $this->em->flush();
            $this->addFlash(
                'success',
                'Votre compte est désormais validé, vous pouvez à présent vous connecter !'
            );
            $flashtype = 'success';
        } else {
            $this->addFlash(
                'warning',
                'La clé est invalide ou a expiré.'
            );
            $flashtype = 'warning';
        }
        $filesystem->mkdir(sys_get_temp_dir() . '/public/assets/img/' . $validation->getUuid(), 0700);
        $filesystem->copy(sys_get_temp_dir() . '/public/assets/img/defaultProfilPicture.jpg', sys_get_temp_dir() . '/public/assets/img/' . $validation->getUuid() . 'profil.jpg');
        $filesystem->mkdir(sys_get_temp_dir() . '/public/assets/img/publications/' . $validation->getUuid(), 0700);
        return $this->forward('App\Controller\SecurityController::login', ['flash' => $flashtype]);
    }
    
}