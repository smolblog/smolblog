<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Connection\Services\ConnectionHandler;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Test\ModelTest;

/**
 * Provices a ConnectionHandler with key 'testmock'
 */
// abstract class ChannelHandlerTestBase implements ConnectionHandler {
// 	public static function getKey(): string {
// 		return 'testmock';
// 	}
// }

abstract class ChannelTestBase extends ModelTest {
	const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected ChannelRepo & MockObject $channels;
	protected SitePermissionsService & MockObject $perms;

	protected function createMockServices(): array {
		$this->channels = $this->createMock(ChannelRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);

		return [
			ChannelRepo::class => fn() => $this->channels,
			SitePermissionsService::class => fn() => $this->perms,
		];
	}
}
