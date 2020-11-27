<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\SideImgRepository;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("/forgot_password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, SideImgRepository $repository)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->sideImg  = $repository->getRandom();
        $this->pageInfos = [
            'sideImg'       => $this->sideImg
        ];
    }
     


    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="app_forgot_password_request")
     */
    public function request(Request $request, \Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        $this->pageInfos = $this->pageInfos + [
            'sideImg_pos'   => 'right',
            'pagename'      => 'Mot de passe oublié',
            'title'         => 'Mot de passe oublié ?',
            'icon'          => 'far fa-question-circle',
            'btn'           => 'Envoi du mail',
            'link'          => [
                ['txt' => 'Connexion', 'href' => '/login'],
                ['txt' => 'Pas encore inscrit ?', 'href' => '/register']
            ]
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer
            );
        }

        return $this->render("login/base.html.twig", [
            'page' => 'forgot',
            'data' => $this->pageInfos,
            'flashtype' => '',
            'form' => $form->createView()
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        // We prevent users from directly accessing this page
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $this->pageInfos = $this->pageInfos + [
            'sideImg_pos'   => 'right',
            'pagename'      => 'Mot de passe oublié',
            'title'         => 'Mot de passe oublié ?',
            'icon'          => 'far fa-question-circle',
            'btn'           => 'Envoi du mail',
            'link'          => [
                ['txt' => 'Connexion', 'href' => '/login'],
                ['txt' => 'Pas encore inscrit ?', 'href' => '/register']
            ]
        ];

        $this->addFlash(
        'success',
        "Un mail a été envoyé à l'adresse indiqué. Le lien sera valable pendant 1 heure. Si vous n'avez rien reçu, vérifiez vos spams ou recommencez le processus.");

        return $this->render("login/base.html.twig", [
            'page' => 'forgot',
            'data' => $this->pageInfos,
            'flashtype' => '',
            'form' => $form->createView()
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            $this->addFlash(
                'success',
                "Le mot de passe a bien été changée !");

            return $this->redirectToRoute('app_login');
        }
        $this->pageInfos = $this->pageInfos + [
            'sideImg_pos'   => 'right',
            'pagename'      => 'Réinitialiser mot de passe',
            'title'         => 'Réinitialiser mot de passe',
            'icon'          => 'far fa-question-circle',
            'btn'           => 'Modifier le mot de passe',
            'link'          => [
                ['txt' => 'Connexion', 'href' => '/login'],
                ['txt' => 'Pas encore inscrit ?', 'href' => '/register']
            ]
        ];

        return $this->render("login/base.html.twig", [
            'page' => 'recover',
            'data' => $this->pageInfos,
            'flashtype' => '',
            'form' => $form->createView()
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, \Swift_Mailer $mailer): RedirectResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Marks that you are allowed to see the app_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
             $this->addFlash(
                 'warning',
                 'There was a problem handling your password reset request ' . $e->getReason(),
             );

            return $this->redirectToRoute('app_check_email');
        }

        $username = $user->getUsername();

        $message = (new \Swift_Message('Alors, ' . $username . ', on a oublié ?'))
        ->setFrom('admin@wibuu.com')
        ->setTo($user->getEmail())
        ->setBody(
            $this->renderView(
                'reset_password/email.html.twig',
                [
                    'resetToken' => $resetToken,
                    'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
                ]
            ), 'text/html');

        $mailer->send($message);

        return $this->redirectToRoute('app_check_email');
    }
}
