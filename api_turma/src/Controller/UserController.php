<?php

namespace App\Controller;

use App\Entity\Administrador;
use App\Entity\Professor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}
    
    #[Route('/criar-admin', methods: ['POST'])]
    public function criarAdmin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $admin = new Administrador();
        $admin->setNome($data['nome']);
        $admin->setEmail($data['email']);
        $admin->setSenhaHash(password_hash($data['senha'], PASSWORD_DEFAULT));

        $this->em->persist($admin);
        $this->em->flush();

        return new JsonResponse(['message' => 'Administrador criado com sucesso', 'id' => $admin->getId()]);
    }

    #[Route('/criar-professor', methods: ['POST'])]
    public function criarProfessor(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $professor = new Professor();
        $professor->setNome($data['nome']);
        $professor->setEmail($data['email']);
        $professor->setSenhaHash(password_hash($data['senha'], PASSWORD_DEFAULT));

        $this->em->persist($professor);
        $this->em->flush();

        return new JsonResponse(['message' => 'Professor criado com sucesso', 'id' => $professor->getId()]);
    }
}
