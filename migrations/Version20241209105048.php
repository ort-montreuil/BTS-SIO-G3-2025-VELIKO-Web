<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209105048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('ALTER TABLE station CHANGE station_id station_id BIGINT NOT NULL, ADD PRIMARY KEY (station_id)');
        $this->addSql('ALTER TABLE user ADD boolean_changer_mdp TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
       // $this->addSql('DROP INDEX `primary` ON station');
        //$this->addSql('ALTER TABLE station CHANGE station_id station_id INT NOT NULL');
        $this->addSql('ALTER TABLE `user` DROP boolean_changer_mdp');
    }
}
