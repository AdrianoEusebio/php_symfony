<?php

namespace App\Controller;

use App\Entity\Administrador;
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
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/professor', methods: ['POST'])]
    public function criarProfessor(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        if (!isset($data['nome'], $data['cpf'], $data['email'], $data['senha'])) {
            return $this->json(['error' => 'Dados incompletos'], 400);
        }

        $prof = new Professor();
        $prof->setNome($data['nome']);
        $prof->setCpf($data['cpf']);
        $prof->setEmail($data['email']);

        $hashed = $this->passwordHasher->hashPassword($prof, $data['senha']);
        $prof->setSenhaHash($hashed);

        $this->em->persist($prof);
        $this->em->flush();

        return $this->json(['message' => 'Professor Cadastrado'], 201);
    }

    #[Route('/aluno', methods: ['POST'])]
    public function criarAluno(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['error' => 'JSON inválido'], 400);
        }

        if (!isset($data['nome'], $data['matricula'])) {
            return $this->json(['error' => 'Dados incompletos'], 400);
        }

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
        if (!$professor) {
            return $this->json(['error' => 'Professor não encontrado']);
        }

        $alunos = $this->em->getRepository(Aluno::class)->findBy(['id' => $data['alunos_ids']]);

        $turma = new Turma();
        $turma->setSerie($data['serie']);
        $turma->setMateria($data['materia']);
        $turma->setProfessor($professor);

        foreach ($alunos as $aluno) {
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

        $data = [];

        foreach ($professores as $professor) {
            $data[] = [
                'id' => $professor->getId(),
                'nome' => $professor->getNome(),
                'cpf' => $professor->getCpf(),
                'email' => $professor->getEmail()
            ];
        }
        return $this->json($data);
    }

    #[Route('/admins', methods: ['GET'])]
    public function listAdmins(): Response
    {
        $admins = $this->em->getRepository(Administrador::class)->findAll();

        $data = [];

        foreach ($admins as $admin) {
            $data[] = [
                'id' => $admin->getId(),
                'nome' => $admin->getNome(),
                'email' => $admin->getEmail()
            ];
        }

        return $this->json($data);
    }

    #[Route('/alunos', methods: ['GET'])]
    public function listAlunos(): Response
    {
        $alunos = $this->em->getRepository(Aluno::class)->findAll();

        foreach ($alunos as $aluno) {
            $data[] = [
                'id' => $aluno->getId(),
                'nome' => $aluno->getName(),
                'matricula' => $aluno->getMatricula()
            ];
        }

        return $this->json($data);
    }

    #[Route('/turmas', methods: ['GET'])]
    public function listTurma(): Response
    {
        $turmas = $this->em->getRepository(Turma::class)->findAll();

        foreach ($turmas as $turma) {

            $alunos = [];
            foreach ($turma->getAlunos() as $aluno) {
                $alunos[] = $aluno->getId();
            }

            $data[] = [
                'id' => $turma->getId(),
                'serie' => $turma->getSerie(),
                'materia' => $turma->getMateria(),
                'professor_id' => $turma->getProfessor()->getId(),
                'alunos_id' => $alunos,
            ];
        }

        return $this->json($data);
    }
}
