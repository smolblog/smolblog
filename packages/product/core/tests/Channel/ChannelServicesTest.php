<?php

namespace Smolblog\Core\Channel;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\ChannelHandlerConfiguration;
use Smolblog\Core\Channel\Services\ChannelDataService;
use Smolblog\Core\Channel\Services\ChannelHandlerRegistry;
use Smolblog\Test\ChannelTestBase;

final class ChannelServicesTest extends ChannelTestBase {
	public function testChannelHandlerCanProvideAllRegisteredHandlers() {
		$handlers = $this->app->container->get(ChannelHandlerRegistry::class)->availableChannelHandlers();

		$this->assertArrayHasKey('testmock', $handlers);
		$this->assertArrayHasKey('asyncmock', $handlers);
		$this->assertArrayHasKey('projectionmock', $handlers);
		$this->assertContainsOnlyInstancesOf(ChannelHandlerConfiguration::class, $handlers);
	}

	public function testChannelDataServiceWillQuerySiteChannelsIfPermissioned() {
		$this->perms->method('canManageChannels')->willReturn(true);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->channels->expects($this->once())->method('channelsForSite');

		$this->app->container->get(ChannelDataService::class)
			->channelsForSite(siteId: $this->randomId(), userId: $this->randomId());
	}

	public function testChannelDataServiceWillQuerySiteChannelsIfCanManageChannels() {
		$this->perms->method('canManageChannels')->willReturn(true);
		$this->perms->method('canPushContent')->willReturn(false);

		$this->channels->expects($this->once())->method('channelsForSite');

		$this->app->container->get(ChannelDataService::class)
			->channelsForSite(siteId: $this->randomId(), userId: $this->randomId());
	}

	public function testChannelDataServiceWillQuerySiteChannelsIfCanPushContent() {
		$this->perms->method('canManageChannels')->willReturn(false);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->channels->expects($this->once())->method('channelsForSite');

		$this->app->container->get(ChannelDataService::class)
			->channelsForSite(siteId: $this->randomId(), userId: $this->randomId());
	}

	public function testChannelDataServiceWillNotQuerySiteChannelsIfNotPermissioned() {
		$this->perms->method('canManageChannels')->willReturn(false);
		$this->perms->method('canPushContent')->willReturn(false);

		$this->channels->expects($this->never())->method('channelsForSite');

		$result = $this->app->container->get(ChannelDataService::class)
			->channelsForSite(siteId: $this->randomId(), userId: $this->randomId());
		$this->assertEquals([], $result);
	}
}
