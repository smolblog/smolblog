<?php

namespace Smolblog\Core\Channel\Commands;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Channel\Events\ChannelAddedToSite;
use Smolblog\Core\Test\ChannelTestBase;

#[AllowMockObjectsWithoutExpectations]
final class AddChannelToSiteTest extends ChannelTestBase {
	public function testAuthorizedIfUsersMatchAndPermissioned() {
		$siteId = $this->randomId();
		$userId = $this->randomId();
		$channel = new BasicChannel(
			handler: 'test',
			handlerKey: 'test',
			displayName: 'Test',
			details: [],
			userId: $userId,
		);

		$this->channels->expects($this->once())->method('channelById')
			->with(channelId: $this->uuidEquals($channel->id))
			->willReturn($channel);
		$this->perms->method('canManageChannels')->willReturn(true);

		$command = new AddChannelToSite(
			channelId: $channel->id,
			siteId: $siteId,
			userId: $userId,
		);

		$this->expectEvent(new ChannelAddedToSite(
			aggregateId: $siteId,
			entityId: $channel->id,
			userId: $userId,
		));

		$this->app->execute($command);
	}

	public function testAuthorizedIfNoUserAndPermissioned() {
		$siteId = $this->randomId();
		$userId = $this->randomId();
		$channel = new BasicChannel(
			handler: 'test',
			handlerKey: 'test',
			displayName: 'Test',
			details: [],
		);

		$this->channels->expects($this->once())->method('channelById')
			->with(channelId: $this->uuidEquals($channel->id))
			->willReturn($channel);
		$this->perms->method('canManageChannels')->willReturn(true);

		$command = new AddChannelToSite(
			channelId: $channel->id,
			siteId: $siteId,
			userId: $userId,
		);

		$this->expectEvent(new ChannelAddedToSite(
			aggregateId: $siteId,
			entityId: $channel->id,
			userId: $userId,
		));

		$this->app->execute($command);
	}

	public function testUnauthorizedIfUsersDoNotMatch() {
		$siteId = $this->randomId();
		$userId = $this->randomId();
		$channel = new BasicChannel(
			handler: 'test',
			handlerKey: 'test',
			displayName: 'Test',
			details: [],
			userId: $this->randomId(),
		);

		$this->channels->expects($this->once())->method('channelById')
			->with(channelId: $this->uuidEquals($channel->id))
			->willReturn($channel);
		$this->perms->method('canManageChannels')->willReturn(true);

		$command = new AddChannelToSite(
			channelId: $channel->id,
			siteId: $siteId,
			userId: $userId,
		);

		$this->expectNoEvents();
		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testUnauthorizedIfUserNotPermissioned() {
		$siteId = $this->randomId();
		$userId = $this->randomId();
		$channel = new BasicChannel(
			handler: 'test',
			handlerKey: 'test',
			displayName: 'Test',
			details: [],
			userId: $userId,
		);

		$this->channels->expects($this->once())->method('channelById')
			->with(channelId: $this->uuidEquals($channel->id))
			->willReturn($channel);
		$this->perms->method('canManageChannels')->willReturn(false);

		$command = new AddChannelToSite(
			channelId: $channel->id,
			siteId: $siteId,
			userId: $userId,
		);

		$this->expectNoEvents();
		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheChannelDoesNotExist() {
		$siteId = $this->randomId();
		$userId = $this->randomId();

		$this->channels->method('channelById')->willReturn(null);
		$this->perms->method('canManageChannels')->willReturn(false);

		$command = new AddChannelToSite(
			channelId: $this->randomId(),
			siteId: $siteId,
			userId: $userId,
		);

		$this->expectNoEvents();
		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}
}
