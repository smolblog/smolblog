<?php

namespace Smolblog\Core\Connector\Data;

use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ChannelDeleted;
use Smolblog\Core\Connector\Events\ConnectionDeleted;
use Smolblog\Core\Connector\Events\connectionsaved;
use Smolblog\Core\Connector\Events\ConnectionEstablished;
use Smolblog\Core\Connector\Events\ConnectionRefreshed;
use Smolblog\Core\Connector\Queries\ChannelById;
use Smolblog\Core\Connector\Queries\ConnectionBelongsToUser;
use Smolblog\Core\Connector\Queries\ConnectionById;
use Smolblog\Core\Connector\Queries\connectionsForConnection;
use Smolblog\Core\Connector\Queries\ConnectionsForUser;
use Smolblog\Core\User\User;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class ConnectionProjectionTest extends TestCase {
	use DatabaseTestKit;

	private ConnectionProjection $projection;

	protected function setUp(): void {
		$this->initDatabaseWithTable('connections', function(Blueprint $table) {
			$table->uuid('connection_uuid')->primary();
			$table->uuid('user_uuid');
			$table->string('provider');
			$table->string('provider_key');
			$table->string('display_name');
			$table->jsonb('details');
		});
		$this->projection = new ConnectionProjection($this->db);
	}

	private function setUpTestConnection(): Connection {
		$connection = new Connection(
			userId: $this->randomId(scrub: true),
			provider: 'mastofed',
			providerKey: '123456',
			displayName: 'Test Account',
			details: ['one' => 'two'],
		);

		$this->db->table('connections')->insert([
			'connection_uuid' => $connection->id->toString(),
			'user_uuid' => $connection->userId->toString(),
			'provider' => 'mastofed',
			'provider_key' => '123456',
			'display_name' => 'Test Account',
			'details' => '{"one":"two"}',
		]);

		return $connection;
	}

	public function testItWillSaveANewConnection() {
		$event = new ConnectionEstablished(
			userId: $this->randomId(),
			provider: 'mastofed',
			providerKey: '123456',
			displayName: 'Test Account',
			details: ['token' => '4edefc8f-0720-4fc1-8fe0-4db7882f7096'],
		);

		$this->projection->onConnectionEstablished($event);

		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('connections'),
			connection_uuid: $event->connectionId->toString(),
			user_uuid: $event->userId->toString(),
			provider: 'mastofed',
			provider_key: '123456',
			display_name: 'Test Account',
			details: '{"token":"4edefc8f-0720-4fc1-8fe0-4db7882f7096"}',
		);
	}

	public function testItWillUpdateAnExistingConnection() {
		$connection = $this->setUpTestConnection();

		$event = new ConnectionEstablished(
			userId: $this->randomId(),
			provider: $connection->provider,
			providerKey: $connection->providerKey,
			displayName: 'Group Account',
			details: ['token' => 'ffd9dc66-ca99-477e-9913-aa171e70cc32'],
		);

		$this->projection->onConnectionEstablished($event);

		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('connections'),
			connection_uuid: $event->connectionId->toString(),
			user_uuid: $event->userId->toString(),
			provider: 'mastofed',
			provider_key: '123456',
			display_name: 'Group Account',
			details: '{"token":"ffd9dc66-ca99-477e-9913-aa171e70cc32"}',
		);
	}

	public function testItWillRefreshAConnection() {
		$connection = $this->setUpTestConnection();

		$this->projection->onConnectionRefreshed(new ConnectionRefreshed(
			details: ['token' => 'c61653bb-9ac1-472e-b838-83fbaf2d26a6'],
			connectionId: $connection->id,
			userId: Identifier::fromString(User::INTERNAL_SYSTEM_USER_ID), //action may be taken by another actor
		));

		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('connections'),
			connection_uuid: $connection->id->toString(),
			user_uuid: $connection->userId,
			provider: 'mastofed',
			provider_key: '123456',
			display_name: 'Test Account',
			details: '{"token":"c61653bb-9ac1-472e-b838-83fbaf2d26a6"}',
		);
	}

	public function testItWillDeleteAConnection() {
		$connection = $this->setUpTestConnection();

		$this->projection->onConnectionDeleted(new ConnectionDeleted(
			connectionId: $connection->id,
			userId: $this->randomId(),
		));

		$this->assertTableEmpty($this->db->table('connections'));
	}

	public function testItWillFindASingleChannel() {
		$connection = $this->setUpTestConnection();
		$query = new ConnectionById($connection->id);

		$this->projection->onConnectionById($query);

		$this->assertEquals($connection, $query->results());
	}

	public function testItWillFindAllConnectionsForAUser() {
		$connection1 = $this->setUpTestConnection();
		$connection2 = new Connection(
			userId: $connection1->userId,
			provider: 'bluesite',
			providerKey: '56789',
			displayName: 'Sample Account',
			details: ['three' => 'four'],
		);
		$query = new ConnectionsForUser($connection1->userId);

		$this->db->table('connections')->insert([
			'connection_uuid' => $connection2->id->toString(),
			'user_uuid' => $connection2->userId->toString(),
			'provider' => 'bluesite',
			'provider_key' => '56789',
			'display_name' => 'Sample Account',
			'details' => '{"three":"four"}',
		]);

		$this->projection->onConnectionsForUser($query);

		$this->assertEquals([$connection1, $connection2], $query->results());
	}

	public function testItWillEvaluateIfAConnectionBelongsToAUser() {
		$connection = $this->setUpTestConnection();
		$goodQuery = new ConnectionBelongsToUser(connectionId: $connection->id, userId: $connection->userId);
		$badQuery = new ConnectionBelongsToUser(connectionId: $connection->id, userId: $this->randomId());

		$this->projection->onConnectionBelongsToUser($goodQuery);
		$this->projection->onConnectionBelongsToUser($badQuery);

		$this->assertTrue($goodQuery->results());
		$this->assertFalse($badQuery->results());
	}
}
