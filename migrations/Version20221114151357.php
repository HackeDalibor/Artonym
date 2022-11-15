<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221114151357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F7A76ED395');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F723EDC87');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F723EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AA76ED395');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A12469DE2');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user ADD profile_picture_id INT DEFAULT NULL, ADD biography LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649292E8AE2 FOREIGN KEY (profile_picture_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649292E8AE2 ON user (profile_picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C23EDC87');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F7A76ED395');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F723EDC87');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F723EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AA76ED395');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A12469DE2');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649292E8AE2');
        $this->addSql('DROP INDEX UNIQ_8D93D649292E8AE2 ON user');
        $this->addSql('ALTER TABLE user DROP profile_picture_id, DROP biography');
    }
}
