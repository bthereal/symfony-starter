<?php

namespace App\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function loginAction(Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // get the password reset URL env
        $passwordResetUrl = $this->generateUrl('password_reset');

        // get the target path from the session
        $targetPath = $request->getSession()->get('_security.main.target_path', '/');

        return $this->render(
            'security/login.html.twig',
            [
                // last username entered by the user
                'last_username' => $lastUsername,
                'error' => $error,
                'target_path' => $targetPath,
                'passwordResetUrl' => $passwordResetUrl,
            ]
        );
    }
}
