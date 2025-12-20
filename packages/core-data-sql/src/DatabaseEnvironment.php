<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\DBAL\Tools\DsnParser;
use Smolblog\Foundation\Exceptions\CodePathNotSupported;

/**
 * Store the database connection and table prefix.
 *
 * This essentially handles the database connection information. The table prefix, while used extensively by the
 * schema, is considered part of the database configuration since they tend to go hand-in-hand.
 */
class DatabaseEnvironment {
	/**
	 * Database connection.
	 *
	 * @var Connection
	 */
	private Connection $dbalConnection;

	protected const DSN_MAPPING = [
		'mysql' => 'mysqli',
		'postgres' => 'pdo_pgsql',
		'sqlite' => 'pdo_sqlite',
	];

	/**
	 * Create the environment and connect to the given database.
	 *
	 * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/configuration.html#configuration
	 *
	 * @throws CodePathNotSupported If neither $props nor $dsn are provided.
	 *
	 * @param array|null  $props       Array of DBAL connection properties.
	 * @param string|null $dsn         Connection URL for a database.
	 * @param string|null $tablePrefix Optional; will prefix all tables with given string and ignore others.
	 */
	public function __construct(?array $props = null, ?string $dsn = null, private string $tablePrefix = '') {
		if (!isset($props) && !isset($dsn)) {
			throw new CodePathNotSupported('No configuration provided to DatabaseManager.');
		}

		$connectionParameters = $props ?? (new DsnParser(static::DSN_MAPPING))->parse($dsn ?? '');
		$this->dbalConnection = DriverManager::getConnection($connectionParameters);

		if (!empty($tablePrefix)) {
			$this->dbalConnection->getConfiguration()->setSchemaAssetsFilter(
				static function (string|AbstractAsset $assetName) use ($tablePrefix): bool {
					if ($assetName instanceof AbstractAsset) {
						// This statement is included in the Doctrine docs but isn't triggered during tests.
						$assetName = $assetName->getName(); // @codeCoverageIgnore
					}
					return str_starts_with($assetName, $tablePrefix);
				}
			);
		}
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
	 * Add the current tablePrefix to the given table.
	 *
	 * @param string $table Base table name.
	 * @return string
	 */
	public function tableName(string $table): string {
		return $this->tablePrefix . $table;
	}
}
