<?php

namespace App\Controller\Frontend;

use App\Repository\DayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DayFrontendController extends AbstractController
{
    /**
     * @Route("/days/", name="day_frontend")
     */
    public function index(DayRepository $repo): Response
    {
        return $this->render('frontend/day_frontend/index.html.twig', [
            'days' => $repo->findAll(),
        ]);
    }
}
