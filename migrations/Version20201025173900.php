<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025173900 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE follow (id INT AUTO_INCREMENT NOT NULL, follows_id INT DEFAULT NULL, state TINYINT(1) NOT NULL, INDEX IDX_6834447025215351 (follows_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_6834447025215351 FOREIGN KEY (follows_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD follow_id INT DEFAULT NULL, DROP followers, DROP following');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498711D3BC FOREIGN KEY (follow_id) REFERENCES follow (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6498711D3BC ON user (follow_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498711D3BC');
        $this->addSql('DROP TABLE follow');
        $this->addSql('DROP INDEX IDX_8D93D6498711D3BC ON user');
        $this->addSql('ALTER TABLE user ADD followers LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', ADD following LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', DROP follow_id');
    }
}
