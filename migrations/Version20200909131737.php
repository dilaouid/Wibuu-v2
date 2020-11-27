<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200909131737 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE followers followers INT DEFAULT 0, CHANGE following following INT DEFAULT 0, CHANGE publications publications INT DEFAULT 0, CHANGE private private TINYINT(1) DEFAULT \'0\', CHANGE banned banned TINYINT(1) DEFAULT \'0\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE followers followers INT DEFAULT 0 NOT NULL, CHANGE following following INT DEFAULT 0 NOT NULL, CHANGE publications publications INT DEFAULT 0 NOT NULL, CHANGE private private TINYINT(1) DEFAULT NULL, CHANGE banned banned TINYINT(1) DEFAULT NULL');
    }
}
