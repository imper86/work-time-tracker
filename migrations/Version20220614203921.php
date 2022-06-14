<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220614203921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE task (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , last_logged_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_527EDB2577153098 ON task (code)');
        $this->addSql('CREATE INDEX idx_last_logged_at ON task (last_logged_at)');
        $this->addSql(
            'CREATE TABLE work_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
                task_id INTEGER NOT NULL REFERENCES task(id) ON DELETE CASCADE, 
                description VARCHAR(255) NOT NULL, 
                started_at DATETIME NOT NULL, 
                finished_at DATETIME NOT NULL, 
                duration INTEGER NOT NULL
            )'
        );
        $this->addSql('CREATE INDEX idx_task_id ON work_log (task_id)');
        $this->addSql('CREATE INDEX idx_description ON work_log (description)');
        $this->addSql('CREATE INDEX idx_started_at ON work_log (started_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE work_log');
    }
}
