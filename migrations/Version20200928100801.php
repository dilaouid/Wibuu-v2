<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200928100801 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE post_user');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DD395B25E');
        $this->addSql('DROP INDEX IDX_5A8A6C8DD395B25E ON post');
        $this->addSql('ALTER TABLE post ADD private TINYINT(1) NOT NULL, ADD pass VARCHAR(255) NOT NULL, DROP filter_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_user (post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_44C6B1424B89032C (post_id), INDEX IDX_44C6B142A76ED395 (user_id), PRIMARY KEY(post_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE post_user ADD CONSTRAINT FK_44C6B1424B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_user ADD CONSTRAINT FK_44C6B142A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD filter_id INT DEFAULT NULL, DROP private, DROP pass');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DD395B25E FOREIGN KEY (filter_id) REFERENCES filters (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DD395B25E ON post (filter_id)');
    }
}
