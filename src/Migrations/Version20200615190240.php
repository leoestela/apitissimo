<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use DateTime;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200615190240 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create category table and insert default categories';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE category (
                id int(50) AUTO_INCREMENT NOT NULL PRIMARY KEY ,
                name VARCHAR(200) NOT NULL ,
                description VARCHAR(500),
                created_at DATETIME NOT NULL
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE = InnoDB
        ');

    }

    public function postUp(Schema $schema):void
    {
        $dateTime = new DateTime();
        $categoryCollection =
            ['Calefacción', 'Reformas Cocinas', 'Reformas Baños', 'Aire Acondicionado', 'Contrucción Casas'];

        foreach ($categoryCollection as $category){
            $this->connection->insert('category', array(
                'name' => $category,
                'created_at' => $dateTime->format('Y-m-d H:i:s')
            ));
        }
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE category');
    }
}
