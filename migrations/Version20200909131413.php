<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200909131413 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE followers followers INT DEFAULT 0 NOT NULL, CHANGE following following INT DEFAULT 0 NOT NULL, CHANGE publications publications INT DEFAULT 0 NOT NULL, CHANGE activated activated TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE followers followers INT DEFAULT 0, CHANGE following following INT DEFAULT 0, CHANGE publications publications INT DEFAULT 0, CHANGE activated activated TINYINT(1) DEFAULT \'0\'');
    }
}
