<?php

namespace App\Controller;

use App\Entity\User;

use App\Entity\SideImg;
use App\Repository\SideImgRepository;

use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class RegistrationController extends AbstractController
{
    private $emailVerifier;
    private $sideImg;
    private $pageInfos = array();

    public function __construct(EmailVerifier $emailVerifier, SideImgRepository $repository)
    {
        $this->emailVerifier = $emailVerifier;
        $this->sideImg  = $repository->getRandom();
        $this->pageInfos = [
            'sideImg'       => $this->sideImg
        ];
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $filesystem = new Filesystem();
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $flash = '';

        $this->pageInfos = $this->pageInfos + [
            'sideImg_pos'   => 'left',
            'pagename'      => 'Inscription',
            'title'         => 'Inscription',
            'icon'          => 'fas fa-camera-retro',
            'btn'           => 'S\'inscrire',
            'link'          => [
                ['txt' => 'Vous avez déjà un compte ?', 'href' => '/login']
            ]
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setUuid(uniqid());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('noreply@wibuu.com', 'Nibii'))
                        ->to($user->getEmail())
                        ->subject('Bienvenue, ' . $user->getUsername() . ' ! Validez votre compte sans plus tarder !')
                        ->htmlTemplate('emails/registration.html.twig')
                        ->context(['name' => $user->getUsername(),
                        'address' => 'http://localhost:8000'])
                );
                // do anything else you need here, like send an email
                $this->addFlash(
                    'success',
                    'Vous venez de recevoir un e-mail de confirmation pour valider votre compte !'
                );

                $filesystem->mkdir(getcwd() . '/assets/img/' . $user->getUuid(), 0700);
                $filesystem->copy(getcwd() . '/assets/img/defaultProfilPicture.jpg', getcwd() .  '/assets/img/' . $user->getUuid() . '/profil.jpg');
                $filesystem->mkdir(getcwd() . '/assets/img/publications/' . $user->getUuid(), 0700);

                return $this->redirectToRoute('app_login');
            } else {
                $flash = 'warning';
                $this->addFlash(
                    'warning',
                    'Merci de remplir correctement tout les champs.'
                );
            }
        }

        return $this->render("login/base.html.twig", [
            'page' => 'register',
            'flashtype' => $flash,
            'data' => $this->pageInfos,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('warning', $exception->getReason());
            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre compte a bien été validé !');

        return $this->redirectToRoute('app_login');
    }
}
