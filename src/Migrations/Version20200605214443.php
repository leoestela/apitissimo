<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200605214443 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create user table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE user (
                id int(50) AUTO_INCREMENT NOT NULL PRIMARY KEY ,
                email VARCHAR(200) NOT NULL ,
                phone VARCHAR(50) NOT NULL ,
                address VARCHAR(500) NOT NULL ,
                createdAt DATETIME NOT NULL ,
                updatedAt DATETIME NOT NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE user');
    }
}
