<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use Psr\Http\Client\ClientInterface;
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Core\Media\Services\MediaFileRepo;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Test\Stubs\MediaHandlerTestBase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

#[AllowMockObjectsWithoutExpectations]
abstract class MediaTestBase extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected MediaFileRepo&MockObject $fileRepo;
	protected MediaRepo&MockObject $contentRepo;
	protected SitePermissionsService&MockObject $perms;

	protected function createMockServices(): array {
		$this->fileRepo = $this->createMock(MediaFileRepo::class);
		$this->contentRepo = $this->createMock(MediaRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);

		return [
			MediaFileRepo::class => fn() => $this->fileRepo,
			MediaRepo::class => fn() => $this->contentRepo,
			SitePermissionsService::class => fn() => $this->perms,
			ClientInterface::class => Psr18Client::class,
			Psr18Client::class => [],
			...parent::createMockServices(),
		];
	}
}
