<?php

namespace Smolblog\Core\Channel\Services;

use Cavatappi\Foundation\Exceptions\ActionNotAuthorized;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Test\ChannelTestBase;

#[AllowMockObjectsWithoutExpectations]
final class ChannelDataServiceTest extends ChannelTestBase {
	public function testItChecksPermissionsBeforeProvidingData() {
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->perms->expects($this->once())->method('canPushContent')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(true);
		$this->channels->expects($this->once())->method('channelsForSite')->with($this->uuidEquals($siteId))->willReturn([]);

		$result = $this->app->container->get(ChannelDataService::class)->channelsForSite($siteId, $userId);
		$this->assertEquals([], $result);
	}

	public function testItReturnsNothingIfUserCannotPushContent() {
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->perms->expects($this->once())->method('canPushContent')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(false);
		$this->channels->expects($this->never())->method('channelsForSite');

		$actual = $this->app->container->get(ChannelDataService::class)->channelsForSite($siteId, $userId);
		$this->assertEmpty($actual);
	}
}
