<?php

namespace Smolblog\Core\Connector\Data;

use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Events\ChannelDeleted;
use Smolblog\Core\Connector\Events\ChannelSaved;
use Smolblog\Core\Connector\Queries\ChannelById;
use Smolblog\Core\Connector\Queries\ChannelsForConnection;
use Smolblog\Test\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class ChannelProjectionTest extends TestCase {
	use DatabaseTestKit;

	private ChannelProjection $projection;

	protected function setUp(): void {
		$this->initDatabaseWithTable('channels', function(Blueprint $table) {
			$table->uuid('channel_uuid')->primary();
			$table->uuid('connection_uuid');
			$table->string('channel_key');
			$table->string('display_name');
			$table->jsonb('details');
		});
		$this->projection = new ChannelProjection($this->db);
	}

	private function setUpTestChannel(): Channel {
		$channel = new Channel(
			connectionId: $this->randomId(scrub: true),
			channelKey: '123456',
			displayName: 'Test Account',
			details: ['one' => 'two'],
		);

		$this->db->table('channels')->insert([
			'channel_uuid' => $channel->id->toString(),
			'connection_uuid' => $channel->connectionId->toString(),
			'channel_key' => '123456',
			'display_name' => 'Test Account',
			'details' => '{"one":"two"}',
		]);

		return $channel;
	}

	public function testItWillSaveAChannel() {
		$event = new ChannelSaved(
			channelKey: '123456',
			displayName: 'Test Account',
			details: ['token' => '4edefc8f-0720-4fc1-8fe0-4db7882f7096'],
			connectionId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->projection->onChannelSaved($event);

		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('channels'),
			channel_uuid: Channel::buildId($event->connectionId, '123456')->toString(),
			connection_uuid: $event->connectionId->toString(),
			channel_key: '123456',
			display_name: 'Test Account',
			details: '{"token":"4edefc8f-0720-4fc1-8fe0-4db7882f7096"}',
		);
	}

	public function testItWillDeleteAChannel() {
		$channel = $this->setUpTestChannel();

		$this->projection->onChannelDeleted(new ChannelDeleted(
			channelKey: '123456',
			connectionId: $channel->connectionId,
			userId: $this->randomId(),
		));

		$this->assertTableEmpty($this->db->table('channels'));
	}

	public function testItWillFindASingleChannel() {
		$channel = $this->setUpTestChannel();
		$query = new ChannelById($channel->id);

		$this->projection->onChannelById($query);

		$this->assertEquals($channel, $query->results());
	}

	public function testItWillFindAllChannelsForAConnection() {
		$channel1 = $this->setUpTestChannel();
		$channel2 = new Channel(
			connectionId: $channel1->connectionId,
			channelKey: '987654',
			displayName: 'Sample Account',
			details: ['three' => 'four'],
		);
		$query = new ChannelsForConnection($channel1->connectionId);

		$this->db->table('channels')->insert([
			'channel_uuid' => $channel2->id->toString(),
			'connection_uuid' => $channel2->connectionId->toString(),
			'channel_key' => '987654',
			'display_name' => 'Sample Account',
			'details' => '{"three":"four"}',
		]);

		$this->projection->onChannelsForConnection($query);

		$this->assertEquals([$channel1, $channel2], $query->results());
	}
}
