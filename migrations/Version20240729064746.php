<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240729064746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD account_entity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA35FA506 FOREIGN KEY (account_entity_id) REFERENCES account_entity (id)');
        $this->addSql('CREATE INDEX IDX_9474526CA35FA506 ON comment (account_entity_id)');
        $this->addSql('ALTER TABLE conference ADD account_entity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE conference ADD CONSTRAINT FK_911533C8A35FA506 FOREIGN KEY (account_entity_id) REFERENCES account_entity (id)');
        $this->addSql('CREATE INDEX IDX_911533C8A35FA506 ON conference (account_entity_id)');
        $this->addSql('ALTER TABLE todo_list ADD account_entity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE todo_list ADD CONSTRAINT FK_1B199E07A35FA506 FOREIGN KEY (account_entity_id) REFERENCES account_entity (id)');
        $this->addSql('CREATE INDEX IDX_1B199E07A35FA506 ON todo_list (account_entity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todo_list DROP FOREIGN KEY FK_1B199E07A35FA506');
        $this->addSql('DROP INDEX IDX_1B199E07A35FA506 ON todo_list');
        $this->addSql('ALTER TABLE todo_list DROP account_entity_id');
        $this->addSql('ALTER TABLE conference DROP FOREIGN KEY FK_911533C8A35FA506');
        $this->addSql('DROP INDEX IDX_911533C8A35FA506 ON conference');
        $this->addSql('ALTER TABLE conference DROP account_entity_id');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA35FA506');
        $this->addSql('DROP INDEX IDX_9474526CA35FA506 ON comment');
        $this->addSql('ALTER TABLE comment DROP account_entity_id');
    }
}
