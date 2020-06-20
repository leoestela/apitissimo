<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200620140436 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create budget request table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE budget_request (
                id int(50) AUTO_INCREMENT NOT NULL PRIMARY KEY,
                title VARCHAR(100),
                description VARCHAR(500) NOT NULL,
                category_id int(50),
                status VARCHAR(20) NOT NULL,
                applicant_id int(50) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_budget_request_category_id (category_id),
                CONSTRAINT FK_budget_request_category_id FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE CASCADE ON DELETE CASCADE,
                INDEX IDX_budget_request_user_id (applicant_id),
                CONSTRAINT FK_budget_request_user_id FOREIGN KEY (applicant_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE                 
            )DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE budget_request');
    }
}
