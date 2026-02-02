<?php

namespace Smolblog\Core\Connection\Commands;

use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Cavatappi\Foundation\Factories\UuidFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Channel\Events\ChannelDeleted;
use Smolblog\Core\Channel\Events\ChannelSaved;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Test\ConnectionTestBase;

#[AllowMockObjectsWithoutExpectations]
class RefreshChannelsTest extends ConnectionTestBase {
	public function testHappyPathWithCommand() {
		$userId = UuidFactory::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$connection = new Connection(
			userId: $userId,
			handler: 'testmock',
			handlerKey: 'abc1233445',
			displayName: 'Test Account',
			details: ['smol' => 'blog'],
		);

		$command = new RefreshChannels(userId: $userId, connectionId: $connection->id);

		$this->connections->method('connectionById')->willReturn($connection);

		$oldChannel = new BasicChannel(
			handler: 'testmock',
			handlerKey: 'old',
			displayName: 'Old Channel',
			userId: $userId,
			connectionId: $connection->id,
			details: ['authkey' => 'abc'],
		);
		$sameChannel = new BasicChannel(
			handler: 'testmock',
			handlerKey: 'same',
			displayName: 'Same Channel',
			userId: $userId,
			connectionId: $connection->id,
			details: ['authkey' => '123'],
		);
		$newChannel = new BasicChannel(
			handler: 'testmock',
			handlerKey: 'new',
			displayName: 'New Channel',
			userId: $userId,
			connectionId: $connection->id,
			details: ['authkey' => 'doremi'],
		);

		$this->channels->method('channelsForConnection')->willReturn([$oldChannel, $sameChannel]);
		$this->handler->method('getChannels')->willReturn([$sameChannel, $newChannel]);

		$this->expectEvents([
			new ChannelDeleted(entityId: $oldChannel->id, userId: $userId),
			new ChannelSaved(channel: $sameChannel, userId: $userId),
			new ChannelSaved(channel: $newChannel, userId: $userId),
		]);

		$this->app->execute($command);
	}

	public function testInvalidId() {
		$this->expectException(EntityNotFound::class);
		$this->connections->method('connectionById')->willReturn(null);

		$this->app->execute(new RefreshChannels(userId: $this->randomId(), connectionId: $this->randomId()));
	}
}
