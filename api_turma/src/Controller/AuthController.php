<?php

namespace App\Controller;

use App\Entity\Administrador;
use App\Entity\Professor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auth')]
final class AuthController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {}
    
    #[Route('/login',methods:['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $senha = $data['senha'] ?? null;

        if(!$email || !$senha){
            return new JsonResponse(['erro' => 'Email e senha são obrigatorios'],400);
        }

        $admin = $this->em->getRepository(Administrador::class)
            ->findOneBy(['email' => $email]);
        
        if($admin && password_verify($senha, $admin->getPassword())){
            return new JsonResponse([
                'message' => 'Login Adminstrado bem-sucedido',
                'role' => 'admin',
                'id' => $admin->getId()
            ]);
        }

        $professor = $this->em->getRepository(Professor::class)
            ->findOneBy(['email' => $email]);

        if ($professor && password_verify($senha, $professor->getPassword())) {
            return new JsonResponse([
                'message' => 'Login Professor bem-sucedido',
                'role' => 'professor',
                'id' => $professor->getId()
            ]);
        }

        return new JsonResponse(['error' => 'Credenciais inválidas'], 401);
    }
}
