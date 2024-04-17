<?php

namespace Smolblog\Core\Connector\Data;

use Illuminate\Database\Schema\Blueprint;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\ChannelSiteLink;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Events\ChannelSiteLinkSet;
use Smolblog\Core\Connector\Queries\ChannelsForAdmin;
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Connector\Queries\SiteHasPermissionForChannel;
use Smolblog\Core\Connector\Queries\UserCanLinkChannelAndSite;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class ChannelSiteLinkProjectionTest extends TestCase {
	use DatabaseTestKit;

	private ChannelSiteLinkProjection $projection;
	private MessageBus $bus;

	protected function setUp(): void {
		$this->initDatabaseWithTable('channel_site_links', function(Blueprint $table) {
			$table->uuid('link_uuid')->primary();
			$table->uuid('channel_uuid');
			$table->uuid('site_uuid');
			$table->boolean('can_push');
			$table->boolean('can_pull');
		});
		$this->db->getSchemaBuilder()->create('channels', function(Blueprint $table) {
			$table->uuid('channel_uuid')->primary();
			$table->uuid('connection_uuid');
			$table->string('channel_key');
			$table->string('display_name');
			$table->jsonb('details');
		});
		$this->db->getSchemaBuilder()->create('connections', function(Blueprint $table) {
			$table->uuid('connection_uuid')->primary();
			$table->uuid('user_uuid');
			$table->string('provider');
			$table->string('provider_key');
			$table->string('display_name');
			$table->jsonb('details');
		});

		$this->bus = $this->createMock(MessageBus::class);
		$this->projection = new ChannelSiteLinkProjection(db: $this->db, bus: $this->bus);
	}

	private function setUpTestLink() {
		$channel = new Channel(
			connectionId: Identifier::fromString(Connection::buildId('provider', '123456')->toString()),
			channelKey: '123456',
			displayName: 'Test Account',
			details: ['one' => 'two'],
		);
		$link = new ChannelSiteLink(
			channelId: $channel->getId(),
			siteId: $this->randomId(scrub: true),
			canPull: false,
			canPush: true,
		);

		$this->db->table('channel_site_links')->insert([
			'link_uuid' => $link->getId()->toString(),
			'channel_uuid' => $link->channelId->toString(),
			'site_uuid' => $link->siteId->toString(),
			'can_push' => true,
			'can_pull' => false,
		]);
		$this->db->table('channels')->insert([
			'channel_uuid' => $channel->getId()->toString(),
			'connection_uuid' => $channel->connectionId->toString(),
			'channel_key' => '123456',
			'display_name' => 'Test Account',
			'details' => '{"one":"two"}',
		]);

		return [$channel, $link];
	}

	private function setUpManyTestLinks() {
		[$channel1, $link1] = $this->setUpTestLink();
		$channel2 = new Channel(
			connectionId: Identifier::fromString(Connection::buildId('provost', '56789')->toString()),
			channelKey: '56789',
			displayName: 'Sample channel',
			details: ['three' => 'four'],
		);
		$link2 = new ChannelSiteLink(
			channelId: $channel2->getId(),
			siteId: $link1->siteId,
			canPull: true,
			canPush: false,
		);

		$this->db->table('channel_site_links')->insert([
			'link_uuid' => $link2->getId()->toString(),
			'channel_uuid' => $link2->channelId->toString(),
			'site_uuid' => $link2->siteId->toString(),
			'can_push' => false,
			'can_pull' => true,
		]);
		$this->db->table('channels')->insert([
			'channel_uuid' => $channel2->getId()->toString(),
			'connection_uuid' => $channel2->connectionId->toString(),
			'channel_key' => '56789',
			'display_name' => 'Sample channel',
			'details' => '{"three":"four"}',
		]);

		$channel3 = new Channel(
			connectionId: Identifier::fromString(Connection::buildId('processor', '34567')->toString()),
			channelKey: '34567',
			displayName: 'Test channel',
			details: ['five' => 'six'],
		);
		$link3 = new ChannelSiteLink(
			channelId: $channel3->getId(),
			siteId: $this->randomId(scrub: true),
			canPull: true,
			canPush: false,
		);

		$this->db->table('channel_site_links')->insert([
			'link_uuid' => $link3->getId()->toString(),
			'channel_uuid' => $link3->channelId->toString(),
			'site_uuid' => $link3->siteId->toString(),
			'can_push' => false,
			'can_pull' => true,
		]);
		$this->db->table('channels')->insert([
			'channel_uuid' => $channel3->getId()->toString(),
			'connection_uuid' => $channel3->connectionId->toString(),
			'channel_key' => '34567',
			'display_name' => 'Test channel',
			'details' => '{"five":"six"}',
		]);

		return [
			[$channel1, $channel2, $channel3],
			[$link1, $link2, $link3],
		];
	}

	public function testItWillCreateANewLink() {
		$event = new ChannelSiteLinkSet(
			channelId: $this->randomId(),
			siteId: $this->randomId(),
			canPush: true,
			canPull: false,
			connectionId: $this->randomId(),
			userId: $this->randomId(),
		);
		$linkId = ChannelSiteLink::buildId(channelId: $event->channelId, siteId: $event->siteId);

		$this->projection->onChannelSiteLinkSet($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('channel_site_links'),
			link_uuid: $linkId->toString(),
			channel_uuid: $event->channelId->toString(),
			site_uuid: $event->siteId->toString(),
			can_push: true,
			can_pull: false,
		);
	}

	public function testItWillUpdateAnExistingLink() {
		[, $link] = $this->setUpTestLink();
		$event = new ChannelSiteLinkSet(
			channelId: $link->channelId,
			siteId: $link->siteId,
			canPush: false,
			canPull: true,
			connectionId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->projection->onChannelSiteLinkSet($event);

		$this->assertOnlyTableEntryEquals(
			$this->db->table('channel_site_links'),
			link_uuid: $link->getId()->toString(),
			channel_uuid: $link->channelId->toString(),
			site_uuid: $link->siteId->toString(),
			can_push: false,
			can_pull: true,
		);
	}

	public function testItWillRemoveALinkIfPermissionsAreRevoked() {
		[, $link] = $this->setUpTestLink();
		$event = new ChannelSiteLinkSet(
			channelId: $link->channelId,
			siteId: $link->siteId,
			canPush: false,
			canPull: false,
			connectionId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->projection->onChannelSiteLinkSet($event);

		$this->assertTableEmpty($this->db->table('channel_site_links'));
	}

	public function testItWillFindAllChannelsLinkedToASite() {
		[$channels, $links] = $this->setUpManyTestLinks();

		$query = new ChannelsForSite(siteId: $links[0]->siteId);
		$this->projection->onChannelsForSite($query);

		$this->assertEquals([$channels[0], $channels[1]], $query->results());
	}

	public function testItWillFindAllChannelsLinkedToASiteWithTheGivenConstraint() {
		[$channels, $links] = $this->setUpManyTestLinks();

		$pushQuery = new ChannelsForSite(siteId: $links[0]->siteId, canPush: true);
		$this->projection->onChannelsForSite($pushQuery);
		$this->assertEquals([$channels[0]], $pushQuery->results());

		$pullQuery = new ChannelsForSite(siteId: $links[0]->siteId, canPull: true);
		$this->projection->onChannelsForSite($pullQuery);
		$this->assertEquals([$channels[1]], $pullQuery->results());
	}

	public function testItWillEvaluateSitePermissionsForChannels() {
		[$channel, $link] = $this->setUpTestLink();

		$bareQuery = new SiteHasPermissionForChannel(siteId: $link->siteId, channelId: $channel->getId());
		$this->projection->onSiteHasPermissionForChannel($bareQuery);
		$this->assertTrue($bareQuery->results());

		$goodQuery = new SiteHasPermissionForChannel(siteId: $link->siteId, channelId: $channel->getId(), mustPush: true);
		$this->projection->onSiteHasPermissionForChannel($goodQuery);
		$this->assertTrue($goodQuery->results(), print_r([$goodQuery, $link], true));

		$badQuery = new SiteHasPermissionForChannel(siteId: $link->siteId, channelId: $channel->getId(), mustPull: true);
		$this->projection->onSiteHasPermissionForChannel($badQuery);
		$this->assertFalse($badQuery->results());
	}

	public function testItWillDisallowLinkingIfUserDoesNotOwnConnection() {
		[$channel, ] = $this->setUpTestLink();
		$this->db->table('connections')->insert([
			'connection_uuid' => $channel->connectionId->toString(),
			'user_uuid' => $this->randomId()->toString(),
			'provider' => 'provider',
			'provider_key' => '123456',
			'display_name' => 'Test Account',
			'details' => '{"one":"two"}',
		]);

		$query = new UserCanLinkChannelAndSite(userId: $this->randomId(), channelId: $channel->getId(), siteId: $this->randomId());
		$this->projection->onUserCanLinkChannelAndSite($query);
		$this->assertFalse($query->results());
	}

	public function testItWillCheckSitePermissionsIfUserOwnsConnection() {
		[$channel, ] = $this->setUpTestLink();
		$userId = $this->randomId();
		$this->db->table('connections')->insert([
			'connection_uuid' => $channel->connectionId->toString(),
			'user_uuid' => $userId->toString(),
			'provider' => 'provider',
			'provider_key' => '123456',
			'display_name' => 'Test Account',
			'details' => '{"one":"two"}',
		]);

		$query = new UserCanLinkChannelAndSite(userId: $userId, channelId: $channel->getId(), siteId: $this->randomId());

		$this->bus->expects($this->once())->method('fetch')->with($this->equalTo(
			new UserHasPermissionForSite(siteId: $query->siteId, userId: $userId, mustBeAdmin: true)
		));

		$this->projection->onUserCanLinkChannelAndSite($query);
	}

	public function testItWillFindChannelsForTheAdminScreen() {
		$siteId = $this->randomId(scrub: true);
		$connections = [
			new Connection(
				userId: $this->randomId(scrub: true),
				provider: 'microdon',
				providerKey: '44245',
				displayName: '@me@microdon.com',
				details: ['one' => 'two'],
			),
			new Connection(
				userId: $this->randomId(scrub: true),
				provider: 'mastopub',
				providerKey: '34567',
				displayName: '@lager@mastopub.com',
				details: ['three' => 'four'],
			),
		];
		$this->db->table('connections')->insert(array_map(
			fn($con) => [
				'connection_uuid' => $con->getId()->toString(),
				'user_uuid' => $con->userId->toString(),
				'provider' => $con->provider,
				'provider_key' => $con->providerKey,
				'display_name' => $con->displayName,
				'details' => json_encode($con->details),
			],
			$connections
		));

		$channels = [
			new Channel(
				connectionId: $this->scrubId($connections[0]->getId()),
				channelKey: '2345',
				displayName: '@me@microdon.com',
				details: ['five' => 'six'],
			),
			new Channel(
				connectionId: $this->scrubId($connections[0]->getId()),
				channelKey: '4567',
				displayName: '@otherme@microdon.com',
				details: ['seven' => 'eight'],
			),
			new Channel(
				connectionId: $this->scrubId($connections[1]->getId()),
				channelKey: '7750',
				displayName: '@lager@mastopub.com',
				details: ['nine' => 'ten'],
			),
			new Channel(
				connectionId: $this->scrubId($connections[1]->getId()),
				channelKey: '9943',
				displayName: '@pilsner@mastopub.com',
				details: ['eleven' => 'twelve'],
			),
		];
		$this->db->table('channels')->insert(array_map(
			fn($cha) => [
				'channel_uuid' => $cha->getId()->toString(),
				'connection_uuid' => $cha->connectionId->toString(),
				'channel_key' => $cha->channelKey,
				'display_name' => $cha->displayName,
				'details' => json_encode($cha->details),
			],
			$channels
		));

		$links = [
			new ChannelSiteLink(
				channelId: $this->scrubId($channels[0]->getId()),
				siteId: $siteId,
				canPull: true,
				canPush: false,
			),
			new ChannelSiteLink(
				channelId: $this->scrubId($channels[0]->getId()),
				siteId: $this->randomId(),
				canPull: true,
				canPush: false,
			),
			new ChannelSiteLink(
				channelId: $this->scrubId($channels[2]->getId()),
				siteId: $this->randomId(),
				canPull: true,
				canPush: false,
			),
			new ChannelSiteLink(
				channelId: $this->scrubId($channels[3]->getId()),
				siteId: $siteId,
				canPull: true,
				canPush: true,
			),
		];
		$this->db->table('channel_site_links')->insert(array_map(
			fn($link) => [
				'link_uuid' => $link->getId()->toString(),
				'channel_uuid' => $link->channelId->toString(),
				'site_uuid' => $link->siteId->toString(),
				'can_pull' => $link->canPull,
				'can_push' => $link->canPush,
			],
			$links
		));

		$expected = [
			'connections' => [
				$connections[0]->getId()->toString() => $connections[0],
				$connections[1]->getId()->toString() => $connections[1],
			],
			'channels' => [
				$connections[0]->getId()->toString() => [$channels[0], $channels[1]],
				$connections[1]->getId()->toString() => [$channels[3]],
			],
			'links' => [
				$channels[0]->getId()->toString() => $links[0],
				$channels[3]->getId()->toString() => $links[3],
			],
		];

		$query = new ChannelsForAdmin(siteId: $siteId, userId: $connections[0]->userId);
		$this->projection->onChannelsForAdmin($query);

		$this->assertEquals($expected, $query->results());
	}
}
