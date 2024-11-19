<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ChannelHandlerConfiguration;
use Smolblog\Core\Channel\Jobs\ContentPushJob;
use Smolblog\Core\Channel\Services\AsyncChannelHandler;
use Smolblog\Core\Channel\Services\ChannelHandler;
use Smolblog\Core\Channel\Services\ChannelHandlerRegistry;
use Smolblog\Core\Channel\Services\DefaultChannelHandler;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Foundation\Service\Job\JobManager;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Jobs\Job;
use Smolblog\Test\ModelTest;

/**
 * Provices a ChannelHandler with key 'testmock'
 */
abstract class ChannelHandlerTestBase implements ChannelHandler {
	public static function getConfiguration(): ChannelHandlerConfiguration {
		return new ChannelHandlerConfiguration(
			key: 'testmock',
			displayName: 'Test',
		);
	}
}

/**
 * Provices a ChannelHandler with key 'defaultmock'
 */
abstract class DefaultChannelHandlerTestBase extends AsyncChannelHandler {
	public static function getConfiguration(): ChannelHandlerConfiguration {
		return new ChannelHandlerConfiguration(
			key: 'defaultmock',
			displayName: 'Default',
		);
	}

	public function __construct(
		JobManager $jobManager,
		EventDispatcherInterface $eventBus
	) {
		$jobManagerProxy = new class($jobManager) implements JobManager {
			public function __construct(private JobManager $actual) {}
			public function enqueue(Job $job): void {
				if (get_class($job) === ContentPushJob::class) {
					// Since our service is actually an anonymous class, we need to override the job.
					$this->actual->enqueue($job->with(service: DefaultChannelHandlerTestBase::class));
					return;
				}
				$this->actual->enqueue($job);
			}
		};
		parent::__construct($jobManagerProxy, $eventBus);
	}
}

abstract class ChannelTestBase extends ModelTest {
	const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected ChannelRepo & MockObject $channels;
	protected SitePermissionsService & MockObject $perms;
	protected ContentRepo & MockObject $contentRepo;
	protected ChannelHandlerTestBase & MockObject $handlerMock;
	protected DefaultChannelHandlerTestBase & MockObject $defaultHandlerMock;

	protected function createMockServices(): array {
		$this->channels = $this->createMock(ChannelRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);
		$this->contentRepo = $this->createMock(ContentRepo::class);
		$this->handlerMock = $this->createMock(ChannelHandlerTestBase::class);

		return [
			ChannelRepo::class => fn() => $this->channels,
			SitePermissionsService::class => fn() => $this->perms,
			ContentRepo::class => fn() => $this->contentRepo,
			ChannelHandlerTestBase::class => fn() => $this->handlerMock,
			DefaultChannelHandlerTestBase::class => fn() => $this->defaultHandlerMock,
			...parent::createMockServices(),
		];
	}

	protected function setUp(): void {
		parent::setUp();

		$this->defaultHandlerMock = $this
			->getMockBuilder(DefaultChannelHandlerTestBase::class)
			->onlyMethods(['push'])
			->setConstructorArgs([
				'jobManager' => $this->app->container->get(JobManager::class),
				'eventBus' => $this->mockEventBus
			])
			->getMock();
	}
}
