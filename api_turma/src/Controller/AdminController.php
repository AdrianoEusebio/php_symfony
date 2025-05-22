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
use OpenApi\Annotations as OA;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/professor', methods: ['POST'])]
    /**
     * @OA\Post(
     *     summary="Cadastrar Professor",
     *     tags={"Professores"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "cpf", "email", "senha"},
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="cpf", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="senha", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Professor criado com sucesso"),
     *     @OA\Response(response=400, description="Dados inválidos")
     * )
     */
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
    /**
     * @OA\Post(
     *     summary="Cadastrar Aluno",
     *     tags={"Alunos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "matricula"},
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="matricula", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Aluno criado com sucesso"),
     *     @OA\Response(response=400, description="Dados inválidos")
     * )
     */
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
        $aluno->setName($data['nome']); // corrigido de setName para setNome
        $aluno->setMatricula($data['matricula']);

        $this->em->persist($aluno);
        $this->em->flush();

        return $this->json(['message' => 'Aluno cadastrado'], 201);
    }

    #[Route('/turma', methods: ['POST'])]
    /**
     * @OA\Post(
     *     summary="Cadastrar Turma",
     *     tags={"Turmas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"serie", "materia", "professor_id", "alunos_ids"},
     *             @OA\Property(property="serie", type="string"),
     *             @OA\Property(property="materia", type="string"),
     *             @OA\Property(property="professor_id", type="integer"),
     *             @OA\Property(
     *                 property="alunos_ids",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Turma criada com sucesso"),
     *     @OA\Response(response=400, description="Dados inválidos")
     * )
     */
    public function criarTurma(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['serie'], $data['materia'], $data['professor_id'], $data['alunos_ids'])) {
            return $this->json(['error' => 'Dados incompletos'], 400);
        }

        $professor = $this->em->getRepository(Professor::class)->find($data['professor_id']);
        if (!$professor) {
            return $this->json(['error' => 'Professor não encontrado'], 400);
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

        return $this->json(['message' => 'Turma criada'], 201);
    }

    #[Route('/professores', methods: ['GET'])]
    /**
     * @OA\Get(
     *     summary="Listar Professores",
     *     tags={"Professores"},
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function listProfessores(): Response
    {
        $professores = $this->em->getRepository(Professor::class)->findAll();

        $data = array_map(fn($professor) => [
            'id' => $professor->getId(),
            'nome' => $professor->getNome(),
            'cpf' => $professor->getCpf(),
            'email' => $professor->getEmail()
        ], $professores);

        return $this->json($data);
    }

    #[Route('/admins', methods: ['GET'])]
    /**
     * @OA\Get(
     *     summary="Listar Admins",
     *     tags={"Administradores"},
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function listAdmins(): Response
    {
        $admins = $this->em->getRepository(Administrador::class)->findAll();

        $data = array_map(fn($admin) => [
            'id' => $admin->getId(),
            'nome' => $admin->getNome(),
            'email' => $admin->getEmail()
        ], $admins);

        return $this->json($data);
    }

    #[Route('/alunos', methods: ['GET'])]
    /**
     * @OA\Get(
     *     summary="Listar Alunos",
     *     tags={"Alunos"},
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function listAlunos(): Response
    {
        $alunos = $this->em->getRepository(Aluno::class)->findAll();

        $data = array_map(fn($aluno) => [
            'id' => $aluno->getId(),
            'nome' => $aluno->getNome(),
            'matricula' => $aluno->getMatricula()
        ], $alunos);

        return $this->json($data);
    }

    #[Route('/turmas', methods: ['GET'])]
    /**
     * @OA\Get(
     *     summary="Listar Turmas",
     *     tags={"Turmas"},
     *     @OA\Response(response=200, description="Sucesso")
     * )
     */
    public function listTurma(): Response
    {
        $turmas = $this->em->getRepository(Turma::class)->findAll();

        $data = array_map(function ($turma) {
            $alunos = array_map(fn($aluno) => $aluno->getId(), $turma->getAlunos()->toArray());

            return [
                'id' => $turma->getId(),
                'serie' => $turma->getSerie(),
                'materia' => $turma->getMateria(),
                'professor_id' => $turma->getProfessor()->getId(),
                'alunos_id' => $alunos
            ];
        }, $turmas);

        return $this->json($data);
    }
}
