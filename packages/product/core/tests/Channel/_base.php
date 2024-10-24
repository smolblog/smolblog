<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Channel\Data\ChannelRepo;
use Smolblog\Core\Channel\Entities\ChannelHandlerConfiguration;
use Smolblog\Core\Channel\Services\ChannelHandler;
use Smolblog\Core\Channel\Services\ChannelHandlerRegistry;
use Smolblog\Core\Channel\Services\DefaultChannelHandler;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Foundation\Service\Job\JobManager;
use Smolblog\Test\ModelTest;

/**
 * Provices a ChannelHandler with key 'testmock'
 */
abstract class ChannelHandlerTestBase implements ChannelHandler {
	public static function getConfiguration(): ChannelHandlerConfiguration
	{
		return new ChannelHandlerConfiguration(
			key: 'testmock',
			displayName: 'Test',
		);
	}
}

/**
 * Provices a ChannelHandler with key 'testmock'
 */
abstract class DefaultChannelHandlerTestBase extends DefaultChannelHandler {
	public static function getConfiguration(): ChannelHandlerConfiguration
	{
		return new ChannelHandlerConfiguration(
			key: 'defaultmock',
			displayName: 'Default',
		);
	}
}

abstract class ChannelTestBase extends ModelTest {
	const INCLUDED_MODELS = [\Smolblog\Core\Model::class];

	protected ChannelRepo & MockObject $channels;
	protected SitePermissionsService & MockObject $perms;
	protected ContentRepo & MockObject $contentRepo;
	protected ChannelHandlerTestBase & MockObject $handlerMock;
	protected DefaultChannelHandler & MockObject $defaultHandlerMock;
	protected JobManager & MockObject $jobs;

	protected function createMockServices(): array {
		$this->channels = $this->createMock(ChannelRepo::class);
		$this->perms = $this->createMock(SitePermissionsService::class);
		$this->contentRepo = $this->createMock(ContentRepo::class);
		$this->handlerMock = $this->createMock(ChannelHandlerTestBase::class);
		$this->jobs = $this->createMock(JobManager::class);

		$this->defaultHandlerMock = $this
			->getMockBuilder(DefaultChannelHandlerTestBase::class)
			->onlyMethods(['push'])
			->setConstructorArgs(['jobManager' => $this->jobs, 'eventBus' => $this->mockEventBus])
			->getMock();

		return [
			ChannelRepo::class => fn() => $this->channels,
			SitePermissionsService::class => fn() => $this->perms,
			ContentRepo::class => fn() => $this->contentRepo,
			ChannelHandlerTestBase::class => fn() => $this->handlerMock,
			DefaultChannelHandlerTestBase::class => fn() => $this->defaultHandlerMock,
			JobManager::class => fn() => $this->jobs,
		];
	}
}
