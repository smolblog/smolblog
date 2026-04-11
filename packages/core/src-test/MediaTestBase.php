<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientInterface;
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Core\Media\Services\MediaFileRepo;
use Smolblog\Core\Permissions\SitePermissionsService;

#[AllowMockObjectsWithoutExpectations]
abstract class MediaTestBase extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected MediaFileRepo&MockObject $fileRepo;
	protected MediaRepo&MockObject $contentRepo;
	protected SitePermissionsService&MockObject $perms;
	protected ClientInterface&MockObject $http;

	protected function createMockServices(): array {
		$this->fileRepo = $this->createMock(MediaFileRepo::class);
		$this->contentRepo = $this->createMock(MediaRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);
		$this->http = $this->createMock(ClientInterface::class);

		return [
			MediaFileRepo::class => fn() => $this->fileRepo,
			MediaRepo::class => fn() => $this->contentRepo,
			SitePermissionsService::class => fn() => $this->perms,
			ClientInterface::class => fn() => $this->http,
			...parent::createMockServices(),
		];
	}
}
