<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function handle(): Response
    {
        return new Response('<html><body>Hello world!</body></html>');
    }
}
