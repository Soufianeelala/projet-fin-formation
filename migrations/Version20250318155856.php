<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318155856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE performance_car (id INT AUTO_INCREMENT NOT NULL, car_id INT NOT NULL, performance_type_id INT NOT NULL, valeur VARCHAR(255) NOT NULL, INDEX IDX_5BE25BAFC3C6F69F (car_id), INDEX IDX_5BE25BAF669064C9 (performance_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE performance_car ADD CONSTRAINT FK_5BE25BAFC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE performance_car ADD CONSTRAINT FK_5BE25BAF669064C9 FOREIGN KEY (performance_type_id) REFERENCES performance_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE performance_car DROP FOREIGN KEY FK_5BE25BAFC3C6F69F');
        $this->addSql('ALTER TABLE performance_car DROP FOREIGN KEY FK_5BE25BAF669064C9');
        $this->addSql('DROP TABLE performance_car');
    }
}
