<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404033009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_user_network (id UUID NOT NULL, user_id UUID NOT NULL, network_name VARCHAR(255) NOT NULL, network_identity VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C69190B2A76ED395 ON auth_user_network (user_id)');
        $this->addSql('COMMENT ON COLUMN auth_user_network.user_id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('CREATE TABLE auth_users (id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) NOT NULL, status VARCHAR(16) NOT NULL, password_hash VARCHAR(255) DEFAULT NULL, new_email VARCHAR(255) DEFAULT NULL, role VARCHAR(16) NOT NULL, join_confirmation_token_value VARCHAR(255) DEFAULT NULL, join_confirmation_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, password_reset_token_value VARCHAR(255) DEFAULT NULL, password_reset_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, new_email_token_value VARCHAR(255) DEFAULT NULL, new_email_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8A1F49CE7927C74 ON auth_users (email)');
        $this->addSql('COMMENT ON COLUMN auth_users.id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.status IS \'(DC2Type:auth_user_status)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.new_email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.role IS \'(DC2Type:auth_user_role)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.join_confirmation_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.password_reset_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_users.new_email_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE auth_user_network ADD CONSTRAINT FK_C69190B2A76ED395 FOREIGN KEY (user_id) REFERENCES auth_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auth_user_network DROP CONSTRAINT FK_C69190B2A76ED395');
        $this->addSql('DROP TABLE auth_user_network');
        $this->addSql('DROP TABLE auth_users');
    }
}
