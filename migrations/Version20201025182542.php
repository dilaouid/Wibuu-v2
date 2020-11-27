<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025182542 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE follow');
        $this->addSql('ALTER TABLE user ADD followers LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD following LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', ADD await_follow LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE follow (id INT AUTO_INCREMENT NOT NULL, follows_id INT DEFAULT NULL, user VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, state TINYINT(1) NOT NULL, INDEX IDX_6834447025215351 (follows_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_6834447025215351 FOREIGN KEY (follows_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP followers, DROP following, DROP await_follow');
    }
}
