<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/api/authentication_token', name: 'api_authentication_token', methods: ['POST'])]
    public function authenticationToken(): Response
    {
        throw new \Exception('should not be reached');
    }
}
