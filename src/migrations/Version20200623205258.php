<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200623205258 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auto (id INT NOT NULL, model_id INT NOT NULL, workshop_id INT DEFAULT NULL, serial_number CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_at INT NOT NULL, power INT NOT NULL, INDEX IDX_66BA25FA7975B7E7 (model_id), INDEX IDX_66BA25FA1FDCE57C (workshop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auto ADD CONSTRAINT FK_66BA25FA7975B7E7 FOREIGN KEY (model_id) REFERENCES auto_model (id)');
        $this->addSql('ALTER TABLE auto ADD CONSTRAINT FK_66BA25FA1FDCE57C FOREIGN KEY (workshop_id) REFERENCES workshop (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auto');
    }
}
