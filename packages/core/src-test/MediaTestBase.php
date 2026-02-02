<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Core\Media\Services\MediaHandler;
use Smolblog\Core\Permissions\SitePermissionsService;

/**
 * Provices a MediaHandler with key 'testmock'
 */
abstract class MediaHandlerTestBase implements MediaHandler {
	public static function getKey(): string {
		return 'testmock';
	}
}

#[AllowMockObjectsWithoutExpectations]
abstract class MediaTestBase extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected MediaHandler&MockObject $mockHandler;
	protected MediaRepo&MockObject $contentRepo;
	protected SitePermissionsService&MockObject $perms;

	protected function createMockServices(): array {
		$this->mockHandler = $this->createMock(MediaHandlerTestBase::class);
		$this->contentRepo = $this->createMock(MediaRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);

		return [
			MediaHandlerTestBase::class => fn() => $this->mockHandler,
			MediaRepo::class => fn() => $this->contentRepo,
			SitePermissionsService::class => fn() => $this->perms,
			...parent::createMockServices(),
		];
	}
}
