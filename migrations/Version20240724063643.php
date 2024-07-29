<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240724063643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE TABLE account_entity (id INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD account_entity_id INT');
        $this->addSql('UPDATE admin SET account_entity_id = (SELECT id FROM account_entity LIMIT 1) WHERE account_entity_id IS NULL');
        $this->addSql('ALTER TABLE admin MODIFY COLUMN account_entity_id INT NOT NULL');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76A35FA506 FOREIGN KEY (account_entity_id) REFERENCES account_entity (id)');
        $this->addSql('CREATE INDEX IDX_880E0D76A35FA506 ON admin (account_entity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76A35FA506');
        $this->addSql('DROP TABLE account_entity');
        $this->addSql('DROP INDEX IDX_880E0D76A35FA506 ON admin');
        $this->addSql('ALTER TABLE admin DROP account_entity_id');
    }
}
