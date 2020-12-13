<?php

namespace App\Security;

use App\Users\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetHandler
{
    private $mailer;

    private $templating;

    private $generator;

    private $from;

    public function __construct(UrlGeneratorInterface $generator, \Twig_Environment $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->generator = $generator;
        $this->from = '';
    }

    public function handle(User $user, string $token)
    {
        $message = new \Swift_Message();
        $message->setSubject('Password reset request');

        $message->setTo($user->getUsername());
        $message->setFrom($this->from);

        $messageVars = [
            'passwordUrl' => $this->generator->generate('password_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
        ];

        $messageBody = $this->templating->render('security/password-recover-email.html.twig', $messageVars);
        $message->setBody($messageBody, 'text/html');

        $this->mailer->send($message);
    }
}
