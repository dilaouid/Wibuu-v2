<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201031123758 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD attach_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE784F8B7 FOREIGN KEY (attach_id) REFERENCES post (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAE784F8B7 ON notification (attach_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE784F8B7');
        $this->addSql('DROP INDEX IDX_BF5476CAE784F8B7 ON notification');
        $this->addSql('ALTER TABLE notification DROP attach_id');
    }
}
