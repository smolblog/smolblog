<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\Service;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Entities\AuthRequestState;

/**
 * Short-term object storage. Think WordPress transients or session variables.
 */
class ScratchPad implements AuthRequestStateRepo, DatabaseTableHandler, Service {
	/**
	 * Create the content table.
	 *
	 * @param Schema   $schema    Schema to add the content table to.
	 * @param callable $tableName Function to create a prefixed table name from a given table name.
	 * @return Schema
	 */
	public static function addTableToSchema(Schema $schema, callable $tableName): Schema {
		$table = $schema->createTable($tableName('scratch_pad'));
		$table->addColumn('dbid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
		$table->addColumn('key', 'string');
		$table->addColumn('value', 'json');
		$table->addColumn('delete_after', 'datetimetz_immutable');

		$table->addPrimaryKeyConstraint(
			PrimaryKeyConstraint::editor()->setUnquotedColumnNames('dbid')->create(),
		);
		$table->addUniqueIndex(['key']);
		$table->addIndex(['delete_after']);

		return $schema;
	}

	/**
	 * Create the service.
	 *
	 * @param DatabaseService      $db    Working database connection.
	 * @param SerializationService $serde Configured (de)serialization service.
	 */
	public function __construct(
		private DatabaseService $db,
		private SerializationService $serde,
	) {}

	/**
	 * Save the given AuthRequestState
	 *
	 * @param AuthRequestState $state State to save.
	 * @return void
	 */
	public function saveAuthRequestState(AuthRequestState $state): void {
		$this->db->delete('scratch_pad', ['key' => 'AuthRequestState__' . $state->key]);

		$exp = new DateTimeImmutable('now +1 hour');

		$this->db->insert('scratch_pad', [
			'key' => 'AuthRequestState__' . $state->key,
			'value' => $this->serde->toJson($state),
			'delete_after' => $exp->format('Y-m-d H:i:s.u'),
		]);

		$this->deleteExpired();
	}

	/**
	 * Get the given AuthRequestState
	 *
	 * @param string $key Key of the state to retrieve.
	 * @return AuthRequestState|null
	 */
	public function getAuthRequestState(string $key): ?AuthRequestState {
		$query = $this->db->createQueryBuilder();
		$query->select('value')
			->from('scratch_pad')
			->where('key = ?')
			->setParameter(0, "AuthRequestState__{$key}");
		$result = $query->fetchOne();

		$this->deleteExpired();

		if ($result === false) {
			return null;
		}

		// This has to do with different DB engines which we cannot currently test.
		return is_string($result)
			? $this->serde->fromJson($result, as: AuthRequestState::class)
			: $this->serde->fromArray($result, as: AuthRequestState::class); // @codeCoverageIgnore
	}

	public function deleteExpired(): void {
		$query = $this->db->createQueryBuilder();
		$query->delete('scratch_pad')
			->where('delete_after < ?')
			->setParameter(0, new DateTimeImmutable(), 'datetime_immutable');
		$query->executeStatement();
	}
}
