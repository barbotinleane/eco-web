<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407144144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson_done ADD learner_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE lesson_done ADD CONSTRAINT FK_295C26CF6209CB66 FOREIGN KEY (learner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_295C26CF6209CB66 ON lesson_done (learner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson_done DROP FOREIGN KEY FK_295C26CF6209CB66');
        $this->addSql('DROP INDEX IDX_295C26CF6209CB66 ON lesson_done');
        $this->addSql('ALTER TABLE lesson_done DROP learner_id');
    }
}
