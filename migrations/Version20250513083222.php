<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513083222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car_categorie (car_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_4E9F2418C3C6F69F (car_id), INDEX IDX_4E9F2418BCF5E72D (categorie_id), PRIMARY KEY(car_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car_categorie ADD CONSTRAINT FK_4E9F2418C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE car_categorie ADD CONSTRAINT FK_4E9F2418BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69DBCF5E72D');
        $this->addSql('DROP INDEX IDX_773DE69DBCF5E72D ON car');
        $this->addSql('ALTER TABLE car DROP categorie_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car_categorie DROP FOREIGN KEY FK_4E9F2418C3C6F69F');
        $this->addSql('ALTER TABLE car_categorie DROP FOREIGN KEY FK_4E9F2418BCF5E72D');
        $this->addSql('DROP TABLE car_categorie');
        $this->addSql('ALTER TABLE car ADD categorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69DBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_773DE69DBCF5E72D ON car (categorie_id)');
    }
}
