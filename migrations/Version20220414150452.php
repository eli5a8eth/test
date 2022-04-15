<?php

declare(strict_types=1);

// phpcs:ignoreFile
namespace DoctrineMigrations;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220414150452 extends AbstractMigration
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getDescription(): string
    {
        return 'TODO: Describe reason for this migration';
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function isTransactional(): bool
    {
        return false;
    }

    /**
     * @throws Exception
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADDF4C85EA');
        $this->addSql('DROP INDEX IDX_D34A04ADDF4C85EA ON product');
        $this->addSql('ALTER TABLE product CHANGE reviews_count reviews_count INT DEFAULT NULL, CHANGE seller_id_id seller_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADDF4C85EA FOREIGN KEY (seller_id) REFERENCES seller (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D34A04ADDF4C85EA ON product (seller_id)');

    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @throws Exception
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADDF4C85EA');
        $this->addSql('DROP INDEX IDX_D34A04ADDF4C85EA ON product');
        $this->addSql('ALTER TABLE product CHANGE reviews_count reviews_count INT NOT NULL, CHANGE seller_id seller_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADDF4C85EA FOREIGN KEY (seller_id_id) REFERENCES seller (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADDF4C85EA ON product (seller_id_id)');
    }
}
