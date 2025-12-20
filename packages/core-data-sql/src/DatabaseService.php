<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;

/**
 * Service for data-focused services to access the database.
 *
 * This is designed to handle MOST of the grunt-work of always running a table name through tableName() in order to
 * properly prefix the table. While the `insert`, `update`, and `delete` methods do this, the QueryBuilder is the
 * notable exception.
 *
 * Notable improvements that can be made when there's bandwidth:
 * - An `upsert` method that will update a record with the given criteria OR create it if it doesn't exist.
 * - Somehow filtering the table names in a DBAL QueryBuilder???
 */
class DatabaseService {
	/**
	 * Create the service.
	 *
	 * @param DatabaseEnvironment $env    Established database connection.
	 * @param SchemaRegistry      $schema Setup and migrated database schema.
	 */
	public function __construct(
		private DatabaseEnvironment $env,
		private SchemaRegistry $schema,
	) {
	}

	/**
	 * Prefix the given table name with the environment's prefix.
	 *
	 * @param string $baseName Table name to prefix.
	 * @return string Prefixed table name.
	 */
	public function tableName(string $baseName): string {
		return $this->env->tableName($baseName);
	}

	/**
	 * Create a Smolblog QueryBuilder to directly query data. Table names should be passed as-is.
	 *
	 * We'll see if this works...
	 *
	 * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/query-builder.html#sql-query-builder
	 *
	 * @return QueryBuilder
	 */
	public function createQueryBuilder(): QueryBuilder {
		return new QueryBuilder(env: $this->env);
	}

	/**
	 * Create a Doctrine DBAL QueryBuilder to directly query data. Table names need to be run through tableName().
	 *
	 * The function name is verbose, but as the QueryBuilder itself is not aware of the database prefix, the name is a
	 * reminder to process table names with tableName() before passing them to the QueryBuilder.
	 *
	 * TODO: make a QueryBuilder that will automatically prefix tables.
	 *
	 * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/query-builder.html#sql-query-builder
	 *
	 * @return DBALQueryBuilder
	 */
	public function createUnprefixedQueryBuilder(): DBALQueryBuilder {
		return $this->env->getConnection()->createQueryBuilder();
	}

	/**
	 * Insert data into the given table.
	 *
	 * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/data-retrieval-and-manipulation.html#insert
	 *
	 * @param string $table Unprefixed table name.
	 * @param array  $data  Data to insert.
	 * @return integer
	 */
	public function insert(string $table, array $data): int {
		$result = $this->env->getConnection()->insert(
			$this->env->tableName($table),
			$data,
		);

		return intval($result);
	}

	/**
	 * Update data in the given table.
	 *
	 * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/data-retrieval-and-manipulation.html#update
	 *
	 * @param string $table Unprefixed table name.
	 * @param array  $data  Data to change.
	 * @param array  $where Criteria to determine what records to change.
	 * @return integer
	 */
	public function update(string $table, array $data, array $where): int {
		$result = $this->env->getConnection()->update(
			$this->env->tableName($table),
			$data,
			$where,
		);

		return intval($result);
	}

	/**
	 * Delete data in the given table.
	 *
	 * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/4.2/reference/data-retrieval-and-manipulation.html#delete
	 *
	 * @param string $table Unprefixed table name.
	 * @param array  $where Criteria to determine what records to delete.
	 * @return integer
	 */
	public function delete(string $table, array $where): int {
		$result = $this->env->getConnection()->delete(
			$this->env->tableName($table),
			$where,
		);

		return intval($result);
	}
}
