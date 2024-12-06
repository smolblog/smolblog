<?php

namespace Smolblog\Core\Channel\Services;

require_once __DIR__ . '/_base.php';

use Smolblog\Foundation\Exceptions\ActionNotAuthorized;
use Smolblog\Test\ChannelTestBase;

final class ChannelQueryServiceTest extends ChannelTestBase {
	public function testItChecksPermissionsBeforeProvidingData() {
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->perms->expects($this->once())->method('canPushContent')->with($userId, $siteId)->willReturn(true);
		$this->channels->expects($this->once())->method('channelsForSite')->with($siteId)->willReturn([]);

		$result = $this->app->container->get(ChannelQueryService::class)->channelsForSite($siteId, $userId);
		$this->assertEquals([], $result);
	}

	public function testItThrowsExceptionIfUserCannotPushContent() {
		$this->expectException(ActionNotAuthorized::class);

		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->perms->expects($this->once())->method('canPushContent')->with($userId, $siteId)->willReturn(false);
		$this->channels->expects($this->never())->method('channelsForSite');

		$this->app->container->get(ChannelQueryService::class)->channelsForSite($siteId, $userId);
	}
}
