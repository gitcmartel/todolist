<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route('/security/denied', name: 'app_security_denied')]
    public function accessDenied(): Response
    {
        return $this->render('security/denied.html.twig');
    }
}
