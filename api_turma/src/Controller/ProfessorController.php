<?php

namespace App\Controller;

use App\Entity\Professor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfessorController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/turmas/{id}', methods: ['GET'])]
    public function minhasTurmas(int $id): Response
    {
        $professor = $this->em->getRepository(Professor::class)->find($id);

        if (!$professor) {
            return $this->json(['error' => 'Professor não encontrado'], 404);
        }

        $turmas = $professor->getTurmas();

        return $this->json($turmas);
    }
}
