<?php

namespace Smolblog\CoreDataSql;

use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Channel\Events\ChannelAddedToSite;
use Smolblog\Core\Channel\Events\ChannelDeleted;
use Smolblog\Core\Channel\Events\ChannelSaved;
use Smolblog\CoreDataSql\Test\DataTestBase;
use Smolblog\Foundation\Value\Fields\Identifier;
use stdClass;

require_once __DIR__ . '/_base.php';

final class ChannelProjectionTest extends DataTestBase {
	private function setUpTestChannels() {
		$commonSite = Identifier::fromString('dbbf45e0-08c8-4422-829d-742b1415f4dd');
		$commonConnection = Identifier::fromString('2b41ef39-452d-4bfb-a15a-d6c40b74c5dd');

		$channels = [
			'none' => new BasicChannel(
				handler: 'test',
				handlerKey: 'none',
				displayName: 'Test None',
				details: ['one' => 2],
			),
			'sameConnection' => new BasicChannel(
				handler: 'test',
				handlerKey: 'sameConnection',
				displayName: 'Test w/ Same Connection',
				details: ['three' => 4],
				connectionId: $commonConnection,
			),
			'otherConnection' => new BasicChannel(
				handler: 'test',
				handlerKey: 'otherConnection',
				displayName: 'Test w/ Other Connection',
				details: ['five' => 6],
				connectionId: $this->randomId(),
			),
			'sameSite' => new BasicChannel(
				handler: 'test',
				handlerKey: 'sameSite',
				displayName: 'Test w/ Same Site',
				details: ['seven' => 8],
			),
			'sameConnectionSameSite' => new BasicChannel(
				handler: 'test',
				handlerKey: 'sameConnectionSameSite',
				displayName: 'Test w/ Same Connection and Same Site',
				details: ['nine' => 10],
				connectionId: $commonConnection,
			),
			'otherConnectionSameSite' => new BasicChannel(
				handler: 'test',
				handlerKey: 'otherConnectionSameSite',
				displayName: 'Test w/ Other Connection and Same Site',
				details: ['eleven' => 12],
				connectionId: $this->randomId(),
			),
			'otherSite' => new BasicChannel(
				handler: 'test',
				handlerKey: 'otherSite',
				displayName: 'Test w/ Other Site',
				details: ['thirteen' => 14],
			),
			'sameConnectionOtherSite' => new BasicChannel(
				handler: 'test',
				handlerKey: 'sameConnectionOtherSite',
				displayName: 'Test w/ Same Connection and Other Site',
				details: ['fifteen' => 16],
				connectionId: $commonConnection,
			),
			'otherConnectionOtherSite' => new BasicChannel(
				handler: 'test',
				handlerKey: 'otherConnectionOtherSite',
				displayName: 'Test w/ Other Connection and Other Site',
				details: ['seventeen' => 18],
				connectionId: $this->randomId(),
			),
		];

		foreach ($channels as $channel) {
			$this->app->dispatch(new ChannelSaved(
				channel: $channel,
				userId: $this->randomId(),
			));
		}

		foreach (['sameSite','sameConnectionSameSite','otherConnectionSameSite'] as $key) {
			$this->app->dispatch(new ChannelAddedToSite(
				aggregateId: $commonSite,
				entityId: $channels[$key]->getId(),
				userId: $this->randomId(),
			));
		}

		foreach (['otherSite','sameConnectionOtherSite','otherConnectionOtherSite'] as $key) {
			$this->app->dispatch(new ChannelAddedToSite(
				aggregateId: $this->randomId(),
				entityId: $channels[$key]->getId(),
				userId: $this->randomId(),
			));
		}

		return $channels;
 	}

	public function testChannelSaved() {
		$projection = $this->app->container->get(ChannelProjection::class);
		// $db = $this->app->container->get(DatabaseManager::class)->getConnection();
		$channel = new BasicChannel(
			handler: 'test',
			handlerKey: 'dbed88c1-f2d3-401f-8869-8d1ab3e57949',
			displayName: 'Test: dbed88c1',
			details: ['abc' => 123],
		);

		$event = new ChannelSaved(
			channel: $channel,
			userId: $this->randomId(),
		);

		$this->assertNull($projection->channelById($channel->getId()));

		$this->app->dispatch(new ChannelSaved(
			channel: $channel,
			userId: $this->randomId(),
		));
		$this->assertObjectEquals($channel, $projection->channelById($channel->getId()) ?? new stdClass());

		$newChannel = $channel->with(details: ['abc' => 456]);
		$this->app->dispatch(new ChannelSaved(
			channel: $newChannel,
			userId: $this->randomId(),
		));
		$this->assertObjectEquals($newChannel, $projection->channelById($channel->getId()) ?? new stdClass());
	}

	public function testChannelAddedToSite() {
		$projection = $this->app->container->get(ChannelProjection::class);
		$siteId = $this->randomId();
		$channelId = $this->randomId();

		$this->assertFalse($projection->siteCanUseChannel($siteId, $channelId));

		$this->app->dispatch(new ChannelAddedToSite(
			aggregateId: $siteId,
			entityId: $channelId,
			userId: $this->randomId(),
		));
		$this->assertTrue($projection->siteCanUseChannel($siteId, $channelId));

		// Repeating the event does not error.
		$this->app->dispatch(new ChannelAddedToSite(
			aggregateId: $siteId,
			entityId: $channelId,
			userId: $this->randomId(),
		));
		$this->assertTrue($projection->siteCanUseChannel($siteId, $channelId));
	}

	public function testChannelDeleted() {
		$projection = $this->app->container->get(ChannelProjection::class);

		$channel = new BasicChannel(
			handler: 'test',
			handlerKey: 'dbed88c1-f2d3-401f-8869-8d1ab3e57949',
			displayName: 'Test: dbed88c1',
			details: ['abc' => 123],
		);
		$this->app->dispatch(new ChannelSaved(
			channel: $channel,
			userId: $this->randomId(),
		));
		$this->assertObjectEquals($channel, $projection->channelById($channel->getId()) ?? new stdClass());

		$this->app->dispatch(new ChannelDeleted(
			entityId: $channel->getId(),
			userId: $this->randomId(),
		));
		$this->assertNull($projection->channelById($channel->getId()));
	}

	public function testChannelsForConnection() {
		$projection = $this->app->container->get(ChannelProjection::class);
		$channels = $this->setUpTestChannels();

		$expected = [
			$channels['sameConnection'],
			$channels['sameConnectionSameSite'],
			$channels['sameConnectionOtherSite'],
		];
		$connectionId = Identifier::fromString('2b41ef39-452d-4bfb-a15a-d6c40b74c5dd');

		$this->assertEquals($expected, $projection->channelsForConnection($connectionId));
	}

	public function testChannelsForSite() {
		$projection = $this->app->container->get(ChannelProjection::class);
		$channels = $this->setUpTestChannels();

		$expected = [
			$channels['sameSite'],
			$channels['sameConnectionSameSite'],
			$channels['otherConnectionSameSite'],
		];
		$siteId = Identifier::fromString('dbbf45e0-08c8-4422-829d-742b1415f4dd');

		$this->assertEquals($expected, $projection->channelsForSite($siteId));
	}
}
