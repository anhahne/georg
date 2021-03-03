<?php

namespace App\Controller;

use App\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthenticationController extends AbstractController
{
    /**
     * @Rest\Post("/api/login", name="login")
     */
    public function index(Request $req, UserRepository $repo, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $repo->findOneBy(['username' => $req->getUser()]);

        if(!$user || !$passwordEncoder->isPasswordValid($user, $req->getPassword())) {
            return new JsonResponse("User or password are wrong", 401);
        }

        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 3600 // 1 hour expiration
            ]);
        return new JsonResponse("success");
    }
}
