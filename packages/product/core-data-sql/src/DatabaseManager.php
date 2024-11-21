<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Tools\DsnParser;
use Exception;
use Smolblog\Foundation\Service\Registry\Registry;

/**
 * Collect services that handle database tables to assist with creating/migrating schema.
 */
class DatabaseManager implements Registry {
	/**
	 * This registry is for DatabaseTableHandlers.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return DatabaseTableHandler::class;
	}

	/**
	 * Database connection.
	 *
	 * @var Connection
	 */
	private Connection $dbalConnection;

	private const DSN_MAPPING = [
		'mysql' => 'mysqli',
		'postgres' => 'pdo_pgsql',
		'sqlite' => 'pdo_sqlite',
	];

	/**
	 * Create the manager and connect to the given database.
	 *
	 * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/configuration.html#configuration
	 *
	 * @throws Exception If neither $props nor $dsn are provided.
	 *
	 * @param array|null  $props Array of DBAL connection properties.
	 * @param string|null $dsn   Connection URL for a database.
	 */
	public function __construct(?array $props = null, ?string $dsn = null) {
		if (!isset($props) && !isset($dsn)) {
			throw new Exception('No configuration provided to DatabaseManager.');
		}

		$connectionParameters = $props ?? (new DsnParser(static::DSN_MAPPING))->parse($dsn ?? '');
		$this->dbalConnection = DriverManager::getConnection($connectionParameters);
	}

	/**
	 * Get the database connection.
	 *
	 * @return Connection
	 */
	public function getConnection(): Connection {
		return $this->dbalConnection;
	}

	/**
	 * Store the handler services.
	 *
	 * @param class-string<DatabaseTableHandler>[] $configuration Array of DatabaseTableHandler services.
	 * @return void
	 */
	public function configure(array $configuration): void {
		// TODO use array_any to confirm correct config.
		$expectedSchema = array_reduce(
			array: $configuration,
			callback: fn($schema, $srv) => $srv::addTableToSchema($schema),
			initial: new Schema()
		);

		$expectedSchema->createTable('db_manager')->addColumn('schema_version', 'string', ['length' => 32]);
		$schemaVersion = md5(implode(
			array: $expectedSchema->toSql($this->dbalConnection->getDatabasePlatform()),
			separator: ' '
		));

		if ($schemaVersion !== $this->getSchemaVersion()) {
			$this->dbalConnection->createSchemaManager()->migrateSchema($expectedSchema);
			$this->setSchemaVersion($schemaVersion);
		}
	}

	/**
	 * Get the hash of the currently live schema.
	 *
	 * @return string
	 */
	protected function getSchemaVersion(): ?string {
		if (!$this->dbalConnection->createSchemaManager()->tableExists('db_manager')) {
			return null;
		}

		$res = $this->dbalConnection->fetchOne('SELECT schema_version FROM db_manager');
		return $res ? $res : null;
	}

	/**
	 * Save the schema version to the database after migration.
	 *
	 * @param string $version Version string to save.
	 * @return void
	 */
	protected function setSchemaVersion(string $version): void {
		if (empty($this->getSchemaVersion())) {
			$this->dbalConnection->insert('db_manager', ['schema_version' => $version]);
			return;
		}

		$this->dbalConnection->update('db_manager', ['schema_version' => $version]);
	}
}
