<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Services\ConnectionHandler;
use Smolblog\Test\ModelTest;

/**
 * Provices a ConnectionHandler with key 'testmock'
 */
abstract class ConnectionHandlerTestBase implements ConnectionHandler {
	public static function getKey(): string {
		return 'testmock';
	}
}

abstract class ConnectionTestBase extends ModelTest {
	const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected AuthRequestStateRepo & MockObject $stateRepo;
	protected ChannelRepo & MockObject $channels;
	protected ConnectionRepo & MockObject $connections;
	protected ConnectionHandler & MockObject $handler;

	protected function createMockServices(): array {
		$this->stateRepo = $this->createMock(AuthRequestStateRepo::class);
		$this->channels = $this->createMock(ChannelRepo::class);
		$this->connections = $this->createMock(ConnectionRepo::class);
		$this->handler = $this->createMock(ConnectionHandlerTestBase::class);

		return [
			AuthRequestStateRepo::class => fn() => $this->stateRepo,
			ChannelRepo::class => fn() => $this->channels,
			ConnectionRepo::class => fn() => $this->connections,
			ConnectionHandlerTestBase::class => fn() => $this->handler,
			...parent::createMockServices(),
		];
	}
}
