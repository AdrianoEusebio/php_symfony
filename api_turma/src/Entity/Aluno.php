<?php

namespace App\Entity;

use App\Repository\AlunoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlunoRepository::class)]
class Aluno
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 6)]
    private ?string $matricula = null;

    /**
     * @var Collection<int, Turma>
     */
    #[ORM\ManyToMany(targetEntity: Turma::class, mappedBy: 'alunos')]
    private Collection $turmas;

    public function __construct()
    {
        $this->turmas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMatricula(): ?string
    {
        return $this->matricula;
    }

    public function setMatricula(string $matricula): static
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * @return Collection<int, Turma>
     */
    public function getTurmas(): Collection
    {
        return $this->turmas;
    }

    public function addTurma(Turma $turma): static
    {
        if (!$this->turmas->contains($turma)) {
            $this->turmas->add($turma);
            $turma->addAluno($this);
        }

        return $this;
    }

    public function removeTurma(Turma $turma): static
    {
        if ($this->turmas->removeElement($turma)) {
            $turma->removeAluno($this);
        }

        return $this;
    }
}
