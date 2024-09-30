<?php

namespace Smolblog\Core\Connection\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\ConnectionTestBase;

class RefreshChannelsTest extends ConnectionTestBase {
	public function testHappyPathWithCommand() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$connection = new Connection(
			userId: $userId,
			provider: 'testmock',
			providerKey: 'abc1233445',
			displayName: 'Test Account',
			details: ['smol' => 'blog'],
		);

		$command = new RefreshChannels(userId: $userId, connectionId: $connection->getId());

		$this->connections->method('connectionById')->willReturn($connection);

		$oldChannel = new BasicChannel(
			handler: 'testmock',
			handlerKey: 'old',
			displayName: 'Old Channel',
			userId: $userId,
			connectionId: $connection->getId(),
			details: ['authkey' => 'abc'],
		);
		$sameChannel = new BasicChannel(
			handler: 'testmock',
			handlerKey: 'same',
			displayName: 'Same Channel',
			userId: $userId,
			connectionId: $connection->getId(),
			details: ['authkey' => '123'],
		);
		$newChannel = new BasicChannel(
			handler: 'testmock',
			handlerKey: 'new',
			displayName: 'New Channel',
			userId: $userId,
			connectionId: $connection->getId(),
			details: ['authkey' => 'doremi'],
		);

		$this->channels->method('channelsForConnection')->willReturn([$oldChannel, $sameChannel]);
		$this->handler->method('getChannels')->willReturn([$sameChannel, $newChannel]);

		// TODO: figure out how to check for multiple events
		$this->mockEventBus->expects($this->exactly(2))->method('dispatch')->withAnyParameters();

		$this->app->execute($command);
	}

	public function testInvalidId() {
		$this->expectException(EntityNotFound::class);
		$this->connections->method('connectionById')->willReturn(null);

		$this->app->execute(new RefreshChannels(userId: $this->randomId(), connectionId: $this->randomId()));
	}
}
