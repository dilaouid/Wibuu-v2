<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025180954 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE follow ADD follows_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_6834447025215351 FOREIGN KEY (follows_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6834447025215351 ON follow (follows_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498711D3BC');
        $this->addSql('DROP INDEX IDX_8D93D6498711D3BC ON user');
        $this->addSql('ALTER TABLE user DROP follow_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_6834447025215351');
        $this->addSql('DROP INDEX IDX_6834447025215351 ON follow');
        $this->addSql('ALTER TABLE follow DROP follows_id');
        $this->addSql('ALTER TABLE user ADD follow_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498711D3BC FOREIGN KEY (follow_id) REFERENCES follow (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6498711D3BC ON user (follow_id)');
    }
}
