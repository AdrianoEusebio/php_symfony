<?php

namespace App\Controller;

use App\Entity\Aluno;
use App\Entity\Professor;
use App\Entity\Turma;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em,
    private UserPasswordHasherInterface $passwordHasher
    ){}

    #[Route('/professor', methods: ['POST'])]
    public function criarProfessor(Request $request): Response
    {
       $data = json_decode($request->getContent(),true);
       
       $prof = new Professor();
       $prof->setNome($data['nome']);
       $prof->setCpf($data['cpf']);
       $prof->setEmail($data['email']);
       
       $hashed = $this->passwordHasher->hashPassword($prof, $data['senha']);
       $prof->setSenhaHash($hashed);

       $this->em->persist($prof);
       $this->em->flush();

       return $this->json(['message' => 'Professor Cadastrado']);
    }

    #[Route('/aluno', methods: ['POST'])]
    public function criarAluno(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $aluno = new Aluno();
        $aluno->setName($data['nome']);
        $aluno->setMatricula($data['matricula']);
        
        $this->em->persist($aluno);
        $this->em->flush();

        return $this->json(['message' => 'Aluno cadastrado']);
    }

    #[Route('/turma', methods: ['POST'])]
    public function criarTurma(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $professor = $this->em->getRepository(Professor::class)->find($data['professor_id']);
        if(!$professor){
            return $this->json(['error' => 'Professor não encontrado']);
        }

        $alunos = $this->em->getRepository(Aluno::class)->findBy(['id' => $data['alunos_ids']]);

        $turma = new Turma();
        $turma->setSerie($data['serie']);
        $turma->setMateria($data['materia']);
        $turma->setProfessor($professor);

        foreach ($alunos as $aluno){
            $turma->addAluno($aluno);
        }

        $this->em->persist($turma);
        $this->em->flush();

        return $this->json(['message' => 'Turma criada']);
    }

    #[Route('/professores', methods: ['GET'])]
    public function listProfessores(): Response
    {
        $professores = $this->em->getRepository(Professor::class)->findAll();
        return $this->json($professores);
    }
}
