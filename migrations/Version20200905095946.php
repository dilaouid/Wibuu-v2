<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200905095946 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE side_img (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(150) NOT NULL, position VARCHAR(100) NOT NULL, anime VARCHAR(100) NOT NULL, link VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user CHANGE followers followers INT DEFAULT 0, CHANGE following following INT DEFAULT 0, CHANGE publications publications INT DEFAULT 0, CHANGE private private TINYINT(1) DEFAULT 0, CHANGE admin admin TINYINT(1) DEFAULT 0, CHANGE banned banned TINYINT(1) DEFAULT 0');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE side_img');
        $this->addSql('ALTER TABLE user CHANGE followers followers INT DEFAULT 0, CHANGE following following INT DEFAULT 0, CHANGE private private TINYINT(1) DEFAULT \'0\', CHANGE admin admin TINYINT(1) DEFAULT \'0\', CHANGE banned banned TINYINT(1) DEFAULT \'0\', CHANGE publications publications INT DEFAULT 0');
    }
}
