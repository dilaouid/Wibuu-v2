<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201025103834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE identification_post (id INT AUTO_INCREMENT NOT NULL, post_id INT DEFAULT NULL, INDEX IDX_B31F82594B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE identification_post_user (identification_post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7A776C6E81912780 (identification_post_id), INDEX IDX_7A776C6EA76ED395 (user_id), PRIMARY KEY(identification_post_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE identification_post ADD CONSTRAINT FK_B31F82594B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE identification_post_user ADD CONSTRAINT FK_7A776C6E81912780 FOREIGN KEY (identification_post_id) REFERENCES identification_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE identification_post_user ADD CONSTRAINT FK_7A776C6EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE identification_post_user DROP FOREIGN KEY FK_7A776C6E81912780');
        $this->addSql('DROP TABLE identification_post');
        $this->addSql('DROP TABLE identification_post_user');
    }
}
