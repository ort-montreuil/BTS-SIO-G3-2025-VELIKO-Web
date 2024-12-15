<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925162229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `user` (
        `id` INT AUTO_INCREMENT NOT NULL,
        `email` VARCHAR(180) NOT NULL,
        `roles` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT \'(DC2Type:json)\' CHECK (JSON_VALID(`roles`)),
        `password` VARCHAR(255) NOT NULL,
        `ville` VARCHAR(255) NOT NULL,
        `nom` VARCHAR(255) NOT NULL,
        `prenom` VARCHAR(255) NOT NULL,
        `date_naissance` DATE NOT NULL,
        `adresse` VARCHAR(100) NOT NULL,
        `code_postal` VARCHAR(255) NOT NULL,
        UNIQUE INDEX UNIQ_USER_EMAIL (`email`),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `user`');
    }
}
