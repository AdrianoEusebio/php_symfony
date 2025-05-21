<?php

namespace App\Entity;

use App\Repository\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: ProfessorRepository::class)]
class Professor implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[ORM\Column(length: 11)]
    private ?string $cpf = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $senhaHash = null;

    /**
     * @var Collection<int, Turma>
     */
    #[ORM\OneToMany(targetEntity: Turma::class, mappedBy: 'professor')]
    private Collection $turmas;

    public function __construct()
    {
        $this->turmas = new ArrayCollection();
    }

    public function getPassword(): ?string
    {
        return $this->senhaHash;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): static
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function setSenhaHash(string $senhaHash): static
    {
        $this->senhaHash = $senhaHash;

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
            $turma->setProfessor($this);
        }

        return $this;
    }

    public function removeTurma(Turma $turma): static
    {
        if ($this->turmas->removeElement($turma)) {
            // set the owning side to null (unless already changed)
            if ($turma->getProfessor() === $this) {
                $turma->setProfessor(null);
            }
        }

        return $this;
    }
}
