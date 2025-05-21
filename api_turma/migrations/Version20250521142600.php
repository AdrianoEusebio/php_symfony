<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521142600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE administrador (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, cpf VARCHAR(11) NOT NULL, email VARCHAR(255) NOT NULL, senha_hash VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE aluno (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, matricula VARCHAR(6) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE professor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, cpf VARCHAR(11) NOT NULL, email VARCHAR(255) NOT NULL, senha_hash VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE turma (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, professor_id INTEGER DEFAULT NULL, serie VARCHAR(50) NOT NULL, materia VARCHAR(100) NOT NULL, CONSTRAINT FK_2B0219A67D2D84D5 FOREIGN KEY (professor_id) REFERENCES professor (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2B0219A67D2D84D5 ON turma (professor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE turma_aluno (turma_id INTEGER NOT NULL, aluno_id INTEGER NOT NULL, PRIMARY KEY(turma_id, aluno_id), CONSTRAINT FK_D155FB16CEBA2CFD FOREIGN KEY (turma_id) REFERENCES turma (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D155FB16B2DDF7F4 FOREIGN KEY (aluno_id) REFERENCES aluno (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D155FB16CEBA2CFD ON turma_aluno (turma_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D155FB16B2DDF7F4 ON turma_aluno (aluno_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE administrador
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE aluno
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE professor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE turma
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE turma_aluno
        SQL);
    }
}
