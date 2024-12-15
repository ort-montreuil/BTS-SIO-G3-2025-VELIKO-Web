<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241030145951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
CREATE TABLE `station` (
    `station_id` bigint(20) NOT NULL,
    `station_code` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `lat` double NOT NULL,
  `lon` double NOT NULL,
  `capacity` int(11) NOT NULL,
  PRIMARY KEY (`station_id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE station_user (id INT AUTO_INCREMENT NOT NULL, id_station BIGINT NOT NULL, id_user INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE station');
        $this->addSql('DROP TABLE station_user');
    }
}
