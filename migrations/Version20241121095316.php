<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241121095316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', author_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', content_uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', comment VARCHAR(255) NOT NULL, published TINYINT(1) NOT NULL, views INT NOT NULL, INDEX IDX_9474526C3590D879 (author_uuid), INDEX IDX_9474526C1C1DBD63 (content_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE upload (uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', path VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C3590D879 FOREIGN KEY (author_uuid) REFERENCES user (uuid)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C1C1DBD63 FOREIGN KEY (content_uuid) REFERENCES content (uuid)');
        $this->addSql('ALTER TABLE content ADD meta_title VARCHAR(255) NOT NULL, ADD meta_description LONGTEXT NOT NULL, ADD published TINYINT(1) NOT NULL, ADD views INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD enabled TINYINT(1) NOT NULL, ADD age INT DEFAULT NULL, DROP created_at, DROP updated_at');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C3590D879');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C1C1DBD63');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE upload');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL, DROP enabled, DROP age');
        $this->addSql('ALTER TABLE content DROP meta_title, DROP meta_description, DROP published, DROP views');
    }
}
