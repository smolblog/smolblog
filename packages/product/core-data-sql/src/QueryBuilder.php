<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;

/**
 * A shim over the Doctrine QueryBuilder that adds the table prefix where appropriate.
 *
 * @codeCoverageIgnore
 */
class QueryBuilder extends DBALQueryBuilder {
	/**
	 * Create the builder
	 *
	 * @param DatabaseEnvironment $env DB connection and table prefixer.
	 */
	public function __construct(private DatabaseEnvironment $env) {
		parent::__construct($this->env->getConnection());
	}

	/**
	 * Turns the query being built into a bulk delete query that ranges over
	 * a certain table.
	 *
	 * <code>
	 *     $qb = $dbService->createQueryBuilder()
	 *         ->delete('users u')
	 *         ->where('u.id = :user_id')
	 *         ->setParameter(':user_id', 1);
	 * </code>
	 *
	 * @param string $table The table whose rows are subject to the deletion.
	 *
	 * @return $this This QueryBuilder instance.
	 */
	public function delete(string $table): self {
		return parent::delete($this->env->tableName($table));
	}

	/**
	 * Turns the query being built into a bulk update query that ranges over
	 * a certain table
	 *
	 * <code>
	 *     $qb = $dbService->createQueryBuilder()
	 *         ->update('counters c')
	 *         ->set('c.value', 'c.value + 1')
	 *         ->where('c.id = ?');
	 * </code>
	 *
	 * @param string $table The table whose rows are subject to the update.
	 *
	 * @return $this This QueryBuilder instance.
	 */
	public function update(string $table): self {
		return parent::update($this->env->tableName($table));
	}

	/**
	 * Turns the query being built into an insert query that inserts into
	 * a certain table
	 *
	 * <code>
	 *     $qb = $dbService->createQueryBuilder()
	 *         ->insert('users')
	 *         ->values(
	 *             array(
	 *                 'name' => '?',
	 *                 'password' => '?'
	 *             )
	 *         );
	 * </code>
	 *
	 * @param string $table The table into which the rows should be inserted.
	 *
	 * @return $this This QueryBuilder instance.
	 */
	public function insert(string $table): self {
		return parent::insert($this->env->tableName($table));
	}

	/**
	 * Creates and adds a query root corresponding to the table identified by the
	 * given alias, forming a cartesian product with any existing query roots.
	 *
	 * <code>
	 *     $qb = $dbService->createQueryBuilder()
	 *         ->select('u.id')
	 *         ->from('users', 'u')
	 * </code>
	 *
	 * @param string      $table The table.
	 * @param string|null $alias The alias of the table.
	 *
	 * @return $this This QueryBuilder instance.
	 */
	public function from(string $table, ?string $alias = null): self {
		return parent::from($this->env->tableName($table), $alias);
	}

	/**
	 * Creates and adds a join to the query.
	 *
	 * <code>
	 *     $qb = $dbService->createQueryBuilder()
	 *         ->select('u.name')
	 *         ->from('users', 'u')
	 *         ->join('u', 'phonenumbers', 'p', 'p.is_primary = 1');
	 * </code>
	 *
	 * @param string $fromAlias The alias that points to a from clause.
	 * @param string $join      The table name to join.
	 * @param string $alias     The alias of the join table.
	 * @param string $condition The condition for the join.
	 *
	 * @return $this This QueryBuilder instance.
	 */
	public function join(string $fromAlias, string $join, string $alias, ?string $condition = null): self {
		return parent::join($fromAlias, $this->env->tableName($join), $alias, $condition);
	}

	/**
	 * Creates and adds a join to the query.
	 *
	 * <code>
	 *     $qb = $dbService->createQueryBuilder()
	 *         ->select('u.name')
	 *         ->from('users', 'u')
	 *         ->innerJoin('u', 'phonenumbers', 'p', 'p.is_primary = 1');
	 * </code>
	 *
	 * @param string $fromAlias The alias that points to a from clause.
	 * @param string $join      The table name to join.
	 * @param string $alias     The alias of the join table.
	 * @param string $condition The condition for the join.
	 *
	 * @return $this This QueryBuilder instance.
	 */
	public function innerJoin(string $fromAlias, string $join, string $alias, ?string $condition = null): self {
		return parent::innerJoin($fromAlias, $this->env->tableName($join), $alias, $condition);
	}

	/**
	 * Creates and adds a left join to the query.
	 *
	 * <code>
	 *     $qb = $dbService->createQueryBuilder()
	 *         ->select('u.name')
	 *         ->from('users', 'u')
	 *         ->leftJoin('u', 'phonenumbers', 'p', 'p.is_primary = 1');
	 * </code>
	 *
	 * @param string $fromAlias The alias that points to a from clause.
	 * @param string $join      The table name to join.
	 * @param string $alias     The alias of the join table.
	 * @param string $condition The condition for the join.
	 *
	 * @return $this This QueryBuilder instance.
	 */
	public function leftJoin(string $fromAlias, string $join, string $alias, ?string $condition = null): self {
		return parent::leftJoin($fromAlias, $this->env->tableName($join), $alias, $condition);
	}

	/**
	 * Creates and adds a right join to the query.
	 *
	 * <code>
	 *     $qb = $dbService->createQueryBuilder()
	 *         ->select('u.name')
	 *         ->from('users', 'u')
	 *         ->rightJoin('u', 'phonenumbers', 'p', 'p.is_primary = 1');
	 * </code>
	 *
	 * @param string $fromAlias The alias that points to a from clause.
	 * @param string $join      The table name to join.
	 * @param string $alias     The alias of the join table.
	 * @param string $condition The condition for the join.
	 *
	 * @return $this This QueryBuilder instance.
	 */
	public function rightJoin(string $fromAlias, string $join, string $alias, ?string $condition = null): self {
		return parent::rightJoin($fromAlias, $this->env->tableName($join), $alias, $condition);
	}
}
