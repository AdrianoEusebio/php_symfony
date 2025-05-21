<?php

namespace App\Entity;

use App\Repository\TurmaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TurmaRepository::class)]
class Turma
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $serie = null;

    #[ORM\Column(length: 100)]
    private ?string $materia = null;

    #[ORM\ManyToOne(inversedBy: 'turmas')]
    private ?Professor $professor = null;

    /**
     * @var Collection<int, Aluno>
     */
    #[ORM\ManyToMany(targetEntity: Aluno::class, inversedBy: 'turmas')]
    private Collection $alunos;

    public function __construct()
    {
        $this->alunos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function setSerie(string $serie): static
    {
        $this->serie = $serie;

        return $this;
    }

    public function getMateria(): ?string
    {
        return $this->materia;
    }

    public function setMateria(string $materia): static
    {
        $this->materia = $materia;

        return $this;
    }

    public function getProfessor(): ?Professor
    {
        return $this->professor;
    }

    public function setProfessor(?Professor $professor): static
    {
        $this->professor = $professor;

        return $this;
    }

    /**
     * @return Collection<int, Aluno>
     */
    public function getAlunos(): Collection
    {
        return $this->alunos;
    }

    public function addAluno(Aluno $aluno): static
    {
        if (!$this->alunos->contains($aluno)) {
            $this->alunos->add($aluno);
        }

        return $this;
    }

    public function removeAluno(Aluno $aluno): static
    {
        $this->alunos->removeElement($aluno);

        return $this;
    }
}
