<?php

namespace App\Controller;

use App\Entity\Administrador;
use App\Entity\Professor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,
    private UserPasswordHasherInterface $passwordHasher) {}
    
    #[Route('/criar-admin', methods: ['POST'])]
    public function criarAdmin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $admin = new Administrador();
        $admin->setNome($data['nome']);
        $admin->setCpf($data['cpf']);
        $admin->setEmail($data['email']);

        $hashed = $this->passwordHasher->hashPassword($admin,$data['senha']);
        $admin->setSenhaHash($hashed);

        $this->em->persist($admin);
        $this->em->flush();

        return new JsonResponse(['message' => 'Administrador criado com sucesso', 'id' => $admin->getId()]);
    }
}
