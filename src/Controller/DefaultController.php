<?php

namespace App\Controller;

use App\Users\UserPermissions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted(UserPermissions::CREATE, $this->getUser());

        return $this->render('default/dashboard.html.twig');
    }
}