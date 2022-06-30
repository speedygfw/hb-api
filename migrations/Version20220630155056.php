<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220630155056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        


        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, contract_id INT DEFAULT NULL, type SMALLINT NOT NULL, amount DOUBLE PRECISION NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, booking_date DATE NOT NULL, INDEX IDX_E00CEDDEA76ED395 (user_id), INDEX IDX_E00CEDDE2576E0FD (contract_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');


        $this->addSql('CREATE TABLE booking_category (booking_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_3D78874C12469DE2 (category_id), INDEX IDX_3D78874C3301C60 (booking_id), PRIMARY KEY(booking_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');


        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');


        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, rotation SMALLINT NOT NULL, start_date DATE NOT NULL, amount DOUBLE PRECISION NOT NULL, type SMALLINT NOT NULL, last_accomplished DATE DEFAULT NULL, INDEX IDX_E98F2859A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');


        $this->addSql('CREATE TABLE contract_category (contract_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_93FB023E12469DE2 (category_id), INDEX IDX_93FB023E2576E0FD (contract_id), PRIMARY KEY(contract_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
  
       
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, api_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D6497BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        //$this->addSql('ALTER TABLE user AUTO_INCREMENT = 1');
    }

    public function down(Schema $schema): void
    {
        
        $this->addSql('DROP TABLE if exists booking');
        $this->addSql('DROP TABLE if exists booking_category');
        
        $this->addSql('DROP TABLE if exists contract');
        $this->addSql('DROP TABLE if exists contract_category');
        
        //$this->addSql('DROP TABLE if exists contract');
        $this->addSql('DROP TABLE if exists category');
        
        
        
        
        
        $this->addSql('DROP TABLE if exists user');
    }
}
