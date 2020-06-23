<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200623204340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auto ADD workshop_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE auto ADD CONSTRAINT FK_66BA25FA7975B7E7 FOREIGN KEY (model_id) REFERENCES auto_model (id)');
        $this->addSql('ALTER TABLE auto ADD CONSTRAINT FK_66BA25FA1FDCE57C FOREIGN KEY (workshop_id) REFERENCES workshop (id)');
        $this->addSql('CREATE INDEX IDX_66BA25FA1FDCE57C ON auto (workshop_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auto DROP FOREIGN KEY FK_66BA25FA7975B7E7');
        $this->addSql('ALTER TABLE auto DROP FOREIGN KEY FK_66BA25FA1FDCE57C');
        $this->addSql('DROP INDEX IDX_66BA25FA1FDCE57C ON auto');
        $this->addSql('ALTER TABLE auto DROP workshop_id');
    }
}
