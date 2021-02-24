<?php

namespace App\Controller;

use App\Entity\Day;
use App\Repository\DayRepository;
use App\Form\DayType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DayController extends AbstractFOSRestController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @Rest\Get("/api/days", name="days")
     */
    public function index(DayRepository $repo): Response
    {
        $days = $repo->findAll();
        $view = $this->view($days, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/api/days/id/{id}")
     */
    public function getDay(DayRepository $repo, int $id): Response {
        $day = $repo->find($id);
        if($day == null) {
            return new JsonResponse("Did not find specified day entry with ID $id.", 404);
        }

        $view = $this->view($day, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/api/days")
     */
    public function createDay(DayRepository $repo, Request $req): Response {
        $form = $this->createForm(DayType::class);
        $data = json_decode($req->getContent(), true);
        $form->submit($data);
        if($form->isSubmitted() && $form->isValid()) {
            $day = $form->getData();

            $this->em->persist($day);
            $this->em->flush();

            $view = $this->view($day, 201);
            return $this->handleView($view);
        } else {
            $errors = $this->getErrorsFromForm($form);
            return new JsonResponse($errors, 400);
        }
    }

    /**
     * @Rest\Put("/api/days/id/{id}")
     */
    public function updateDay(DayRepository $repo, Request $req, int $id): Response {
        $day = $repo->find($id);
        if($day == null) {
            return new JsonResponse("Did not find specified day entry with ID $id.", 404);
        }

        $form = $this->createForm(DayType::class, $day);
        $data = json_decode($req->getContent(), true);
        $form->submit($data);
        if($form->isSubmitted() && $form->isValid()) {
            $day = $form->getData();

            $this->em->persist($day);
            $this->em->flush();

            $view = $this->view($day, 200);
            return $this->handleView($view);
        } else {
            $errors = $this->getErrorsFromForm($form);
            return new JsonResponse($errors, 400);
        }
    }

    /**
     * @Rest\Delete("/api/days/id/{id}")
     */
    public function deleteDay(DayRepository $repo, int $id): Response {
        $day = $repo->find($id);
        if($day == null) {
            return new JsonResponse("Did not find specified day entry with ID $id.", 404);
        }

        $this->em->remove($day);
        $this->em->flush();

        return new Response(null, 204);
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
}
