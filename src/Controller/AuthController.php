<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $repository)
    {
        $this->encoder = $encoder;
        $this->repository = $repository;
    }

    public function getToken(Request $request): Response
    {
        $jsonData = json_decode($request->getContent());
        if ($jsonData === false) {
            throw new AuthenticationException('Dados inválidos');
        }

        $user = $this->repository->findOneBy(['username' => $jsonData->username]);
        if (is_null($user)) {
            throw new AuthenticationException('Usuário inválido');
        }

        if (!$this->encoder->isPasswordValid($user, $jsonData->password)) {
            throw new AuthenticationException('Senha inválida');
        }

        $token = JWT::encode(['username' => $user->getUsername()], $_ENV['JWT_KEY'], 'HS256');
        return new JsonResponse([
            'access_token' => $token
        ]);
    }
}
