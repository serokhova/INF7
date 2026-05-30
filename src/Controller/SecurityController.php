<?php

namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\PasswordResetTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/{_locale}', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'meta_title' => 'Connexion - ColocChaleureuse',
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Intercepted by the firewall logout key.');
    }

    #[Route('/change-password', name: 'app_change_password', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $current = $form->get('currentPassword')->getData();
            if (!$hasher->isPasswordValid($user, $current)) {
                $form->get('currentPassword')->addError(new \Symfony\Component\Form\FormError('Mot de passe actuel invalide.'));
            } else {
                $user->setPassword($hasher->hashPassword($user, $form->get('newPassword')->getData()));
                $em->flush();
                $this->addFlash('success', 'flash.password_changed');
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
            'meta_title' => 'Changer mon mot de passe',
        ]);
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(
        Request $request,
        UserRepository $users,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        $resetLink = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $users->findOneByEmail($email);

            if ($user !== null) {
                $token = new PasswordResetToken();
                $token->setUser($user);
                $token->setToken(bin2hex(random_bytes(32)));
                $em->persist($token);
                $em->flush();

                $resetLink = $this->generateUrl('app_reset_password', ['token' => $token->getToken()]);
            }

            $this->addFlash('info', 'flash.reset_sent');
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
            'reset_link' => $resetLink,
            'meta_title' => 'Mot de passe oublié',
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        string $token,
        Request $request,
        PasswordResetTokenRepository $tokens,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        $resetToken = $tokens->findValidToken($token);
        if ($resetToken === null) {
            $this->addFlash('error', 'flash.reset_invalid');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $resetToken->getUser();
            $user->setPassword($hasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $resetToken->setUsed(true);
            $em->flush();

            $this->addFlash('success', 'flash.password_reset');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
            'meta_title' => 'Réinitialiser mon mot de passe',
        ]);
    }
}
