<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE performance_type_car DROP FOREIGN KEY FK_FE37647E669064C9');
        $this->addSql('ALTER TABLE performance_type_car DROP FOREIGN KEY FK_FE37647EC3C6F69F');
        $this->addSql('DROP TABLE performance_type_car');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE performance_type_car (performance_type_id INT NOT NULL, car_id INT NOT NULL, INDEX IDX_FE37647E669064C9 (performance_type_id), INDEX IDX_FE37647EC3C6F69F (car_id), PRIMARY KEY(performance_type_id, car_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE performance_type_car ADD CONSTRAINT FK_FE37647E669064C9 FOREIGN KEY (performance_type_id) REFERENCES performance_type (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE performance_type_car ADD CONSTRAINT FK_FE37647EC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
