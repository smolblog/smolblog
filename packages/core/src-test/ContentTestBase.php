<?php

namespace Smolblog\Core\Test;

use Cavatappi\Foundation\Value\ValueKit;
use Cavatappi\Test\ModelTest;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Content\Entities\ContentExtensionConfiguration;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Content\Entities\ContentTypeConfiguration;
use Smolblog\Core\Content\Events\{ContentCreated, ContentDeleted, ContentUpdated};
use Smolblog\Core\Content\Services\ContentExtensionService;
use Smolblog\Core\Content\Services\ContentTypeService;
use Smolblog\Core\Content\Services\DefaultContentExtensionService;
use Smolblog\Core\Content\Services\DefaultContentTypeService;
use Smolblog\Core\Permissions\SitePermissionsService;

abstract class TestContentTypeBase implements ContentType {
	use ValueKit;

	public function __construct(public readonly string $title, public readonly string $body) {}
}

/**
 * Provides a ContentType with key 'testdefault'
 */
final class TestDefaultContentTypeService extends DefaultContentTypeService {
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			key: 'testdefault',
			displayName: 'Test - Default',
			typeClass: TestDefaultContentType::class,
		);
	}
}
final class TestDefaultContentType extends TestContentTypeBase {
	public static function getKey(): string {
		return 'testdefault';
	}
}

/**
 * Provides a ContentType with key 'testevents'
 */
final class TestEventsContentTypeCreated extends ContentCreated {}
final class TestEventsContentTypeUpdated extends ContentUpdated {}
final class TestEventsContentTypeDeleted extends ContentDeleted {}
final class TestEventsContentTypeService extends DefaultContentTypeService {
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			key: 'testevents',
			displayName: 'Test - Events',
			typeClass: TestEventsContentType::class,
		);
	}
	protected const CREATE_EVENT = TestEventsContentTypeCreated::class;
	protected const UPDATE_EVENT = TestEventsContentTypeUpdated::class;
	protected const DELETE_EVENT = TestEventsContentTypeDeleted::class;
}
final class TestEventsContentType extends TestContentTypeBase {
	public static function getKey(): string {
		return 'testevents';
	}
}

/**
 * Provides a ContentType with key 'testcustom'
 */
abstract class TestCustomContentTypeService implements ContentTypeService {
	public static function getConfiguration(): ContentTypeConfiguration {
		return new ContentTypeConfiguration(
			key: 'testcustom',
			displayName: 'Test - Default',
			typeClass: TestCustomContentType::class,
		);
	}
}
final class TestCustomContentType extends TestContentTypeBase {
	public static function getKey(): string {
		return 'testcustom';
	}
}

final class TestDefaultContentExtensionService extends DefaultContentExtensionService {
	public static function getConfiguration(): ContentExtensionConfiguration {
		return new ContentExtensionConfiguration(
			key: 'testdefaultext',
			displayName: 'Test Default Extension',
			extensionClass: TestDefaultContentExtension::class,
		);
	}
}
final readonly class TestDefaultContentExtension implements ContentExtension {
	use ValueKit;
	public function __construct(public string $metaval) {}
}

abstract class TestCustomContentExtensionService implements ContentExtensionService {
	public static function getConfiguration(): ContentExtensionConfiguration {
		return new ContentExtensionConfiguration(
			key: 'testcustomext',
			displayName: 'Test Custom Extension',
			extensionClass: TestCustomContentExtension::class,
		);
	}
}
final readonly class TestCustomContentExtension implements ContentExtension {
	use ValueKit;
	public function __construct(public string $metaval) {}
}

#[AllowMockObjectsWithoutExpectations]
abstract class ContentTestBase extends ModelTest {
	public const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected TestCustomContentTypeService&MockObject $customContentService;
	protected TestCustomContentExtensionService&MockObject $customExtensionService;
	protected ContentRepo&MockObject $contentRepo;
	protected SitePermissionsService&MockObject $perms;

	protected function createMockServices(): array {
		$this->customContentService = $this->createMock(TestCustomContentTypeService::class);
		$this->customExtensionService = $this->createMock(TestCustomContentExtensionService::class);
		$this->contentRepo = $this->createMock(ContentRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);

		return [
			TestDefaultContentTypeService::class => ['eventBus' => EventDispatcherInterface::class],
			TestEventsContentTypeService::class => ['eventBus' => EventDispatcherInterface::class],
			TestCustomContentTypeService::class => fn() => $this->customContentService,
			TestDefaultContentExtensionService::class => [],
			TestCustomContentExtensionService::class => fn() => $this->customExtensionService,
			ContentRepo::class => fn() => $this->contentRepo,
			SitePermissionsService::class => fn() => $this->perms,
			...parent::createMockServices(),
		];
	}
}
