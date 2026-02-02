<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Services\ConnectionHandler;
use Smolblog\Core\Permissions\GlobalPermissionsService;

/**
 * Provices a ConnectionHandler with key 'testmock'
 */
abstract class ConnectionHandlerTestBase implements ConnectionHandler {
	public static function getKey(): string {
		return 'testmock';
	}
}

#[AllowMockObjectsWithoutExpectations]
abstract class ConnectionTestBase extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected AuthRequestStateRepo&MockObject $stateRepo;
	protected ChannelRepo&MockObject $channels;
	protected ConnectionRepo&MockObject $connections;
	protected ConnectionHandler&MockObject $handler;
	protected GlobalPermissionsService&MockObject $globalPerms;

	protected function createMockServices(): array {
		$this->stateRepo = $this->createMock(AuthRequestStateRepo::class);
		$this->channels = $this->createMock(ChannelRepo::class);
		$this->connections = $this->createMock(ConnectionRepo::class);
		$this->handler = $this->createMock(ConnectionHandlerTestBase::class);
		$this->globalPerms = $this->createMock(GlobalPermissionsService::class);

		return [
			AuthRequestStateRepo::class => fn() => $this->stateRepo,
			ChannelRepo::class => fn() => $this->channels,
			ConnectionRepo::class => fn() => $this->connections,
			ConnectionHandlerTestBase::class => fn() => $this->handler,
			GlobalPermissionsService::class => fn() => $this->globalPerms,
			...parent::createMockServices(),
		];
	}
}
