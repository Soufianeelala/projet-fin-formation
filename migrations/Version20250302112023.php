<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302112023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, categorie_id INT NOT NULL, marque VARCHAR(255) NOT NULL, modele VARCHAR(255) NOT NULL, annee INT NOT NULL, INDEX IDX_773DE69DBCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, car_id INT NOT NULL, INDEX IDX_C53D045FC3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motorisation_type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motorisation_type_car (motorisation_type_id INT NOT NULL, car_id INT NOT NULL, INDEX IDX_756327428C6BB293 (motorisation_type_id), INDEX IDX_75632742C3C6F69F (car_id), PRIMARY KEY(motorisation_type_id, car_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE performance_type (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE performance_type_car (performance_type_id INT NOT NULL, car_id INT NOT NULL, INDEX IDX_FE37647E669064C9 (performance_type_id), INDEX IDX_FE37647EC3C6F69F (car_id), PRIMARY KEY(performance_type_id, car_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69DBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE motorisation_type_car ADD CONSTRAINT FK_756327428C6BB293 FOREIGN KEY (motorisation_type_id) REFERENCES motorisation_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE motorisation_type_car ADD CONSTRAINT FK_75632742C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE performance_type_car ADD CONSTRAINT FK_FE37647E669064C9 FOREIGN KEY (performance_type_id) REFERENCES performance_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE performance_type_car ADD CONSTRAINT FK_FE37647EC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69DBCF5E72D');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FC3C6F69F');
        $this->addSql('ALTER TABLE motorisation_type_car DROP FOREIGN KEY FK_756327428C6BB293');
        $this->addSql('ALTER TABLE motorisation_type_car DROP FOREIGN KEY FK_75632742C3C6F69F');
        $this->addSql('ALTER TABLE performance_type_car DROP FOREIGN KEY FK_FE37647E669064C9');
        $this->addSql('ALTER TABLE performance_type_car DROP FOREIGN KEY FK_FE37647EC3C6F69F');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE motorisation_type');
        $this->addSql('DROP TABLE motorisation_type_car');
        $this->addSql('DROP TABLE performance_type');
        $this->addSql('DROP TABLE performance_type_car');
    }
}
