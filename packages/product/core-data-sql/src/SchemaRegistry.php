<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Schema\Schema;
use Smolblog\Foundation\Service\Registry\Registry;

/**
 * Collect services that handle database tables to assist with creating/migrating schema.
 */
class SchemaRegistry implements Registry {
	/**
	 * This registry is for DatabaseTableHandlers.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return DatabaseTableHandler::class;
	}

	/**
	 * Create the service
	 *
	 * @param DatabaseEnvironment $env DB Connection and configuration.
	 */
	public function __construct(private DatabaseEnvironment $env) {
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
			callback: fn($schema, $srv) => $srv::addTableToSchema($schema, $this->env->tableName(...)),
			initial: new Schema()
		);

		$expectedSchema
			->createTable($this->env->tableName('db_manager'))
			->addColumn('schema_version', 'string', ['length' => 32]);
		$schemaVersion = md5(implode(
			array: $expectedSchema->toSql($this->env->getConnection()->getDatabasePlatform()),
			separator: ' '
		));

		if ($schemaVersion !== $this->getSchemaVersion()) {
			$this->env->getConnection()->createSchemaManager()->migrateSchema($expectedSchema);
			$this->setSchemaVersion($schemaVersion);
		}
	}

	/**
	 * Get the hash of the currently live schema.
	 *
	 * @return string
	 */
	protected function getSchemaVersion(): ?string {
		if (!$this->env->getConnection()->createSchemaManager()->tableExists($this->env->tableName('db_manager'))) {
			return null;
		}

		$res = $this->env->getConnection()
			->fetchOne('SELECT schema_version FROM ' . $this->env->tableName('db_manager'));
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
			$this->env->getConnection()->insert($this->env->tableName('db_manager'), ['schema_version' => $version]);
			return;
		}

		$this->env->getConnection()->update($this->env->tableName('db_manager'), ['schema_version' => $version]);
	}
}
