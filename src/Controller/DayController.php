<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DayController extends AbstractFOSRestController
{
    /**
     * @Route("/api/day", name="day")
     */
    public function index(): Response
    {
        $view = $this->view('Hello', 200);
        return $this->handleView($view);
    }
}
