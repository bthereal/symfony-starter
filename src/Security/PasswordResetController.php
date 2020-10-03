<?php

namespace App\Security;

use App\Users\UserRepo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class PasswordResetController extends Controller
{
    private $users;

    private $resetHandler;

    public function __construct(UserRepo $users, PasswordResetHandler $resetHandler)
    {
        $this->users = $users;
        $this->resetHandler = $resetHandler;
    }

    public function resetPasswordRequestAction(Request $request)
    {
        $form = $this->createForm(PasswordResetRequestFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->users->findOneBy(['username' => $form->get('username')->getData()]);

            if (!$user) {
                // fail authentication with a custom error
                throw new AuthenticationException('Username could not be found.');
            }

            $token = $user->generateResetToken(new \DateInterval('PT1H'));
            $this->users->save($user);
            $this->resetHandler->handle($user, $token);

            return $this->redirectToRoute('password_sent');
        }

        return $this->render('security/password-request-reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function resetPasswordAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $token = $request->query->get('token');

        $form = $this->createForm(PasswordResetFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->users->findOneBy(['resetToken' => $token]);

            if (null === $user) {
                $this->addFlash('warning', 'This link is invalid. Please reset your password again.');

                return $this->redirectToRoute('security_login');
            }

            if (false === $user->isResetTokenValid($token)) {
                $this->addFlash('warning', 'This link has expired. Please reset your password again.');

                return $this->redirectToRoute('security_login');
            }

            $password = $passwordEncoder->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);
            $user->clearResetToken();
            $this->users->save($user);
            $this->addFlash('success', 'Your password has been updated.');

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/password-reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function passwordSentAction()
    {
        return $this->render('security/password-sent.html.twig');
    }
}
