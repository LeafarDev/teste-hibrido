<?php

declare(strict_types=1);

namespace TesteHibrido\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200930230029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Clients table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create table clients 
        (
            id int auto_increment,
            name varchar(255) null,
            email varchar(255) null,
            cpf varchar(20) null,
            phone varchar(20) null,
            constraint clients_pk
                primary key (id)
        )');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE clients');

    }
}
