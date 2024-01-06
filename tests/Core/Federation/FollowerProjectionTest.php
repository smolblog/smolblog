<?php

namespace Smolblog\Core\Federation;

use Illuminate\Database\Schema\Blueprint;
use Smolblog\Test\DatabaseTestKit;
use Smolblog\Test\TestCase;

final class FollowerProjectionTest extends TestCase {
	use DatabaseTestKit;

	private FollowerProjection $projection;

	public function setUp(): void {
		$this->initDatabaseWithTable('followers', function(Blueprint $table) {
			$table->uuid('follower_uuid')->primary();
			$table->uuid('site_uuid');
			$table->string('provider');
			$table->string('provider_key');
			$table->string('display_name');
			$table->text('details');
		});

		$this->projection = new FollowerProjection(db: $this->db);
	}

	public function testItWillSaveAFollowerToTheDatabase() {
		$follower = new Follower(
			siteId: $this->randomId(),
			provider: 'mastoweb',
			providerKey: 'acct:123345',
			displayName: 'Someone somewehre',
			details: ['one' => 'two'],
		);

		$event = $this->createStub(FollowerAdded::class);
		$event->method('getFollower')->willReturn($follower);

		$this->projection->onFollowerAdded($event);

		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('followers'),
			follower_uuid: $follower->id->toString(),
			site_uuid: $follower->siteId->toString(),
			provider: 'mastoweb',
			provider_key: 'acct:123345',
			display_name: 'Someone somewehre',
			details: '{"one":"two"}',
		);
	}

	public function testItWillRemoveFollowersFromTheDatabase() {
		$siteId = $this->randomId();
		$follower1 = Follower::buildId(siteId: $siteId, provider: 'abc', providerKey: '123');
		$follower2 = Follower::buildId(siteId: $siteId, provider: 'abc', providerKey: '456');

		$this->db->table('followers')->insert([
			[
				'follower_uuid' => $follower1->toString(),
				'site_uuid' => $siteId->toString(),
				'provider' => 'abc',
				'provider_key' => '123',
				'display_name' => 'def',
				'details' => '{}',
			],
			[
				'follower_uuid' => $follower2->toString(),
				'site_uuid' => $siteId->toString(),
				'provider' => 'abc',
				'provider_key' => '456',
				'display_name' => 'ghi',
				'details' => '{}',
			],
		]);

		$this->projection->onFollowerRemoved(new FollowerRemoved(
			siteId: $siteId,
			userId: $this->randomId(),
			provider: 'abc',
			providerKey: '123',
		));

		$this->assertOnlyTableEntryEquals(
			table: $this->db->table('followers'),
			follower_uuid: $follower2->toString(),
			site_uuid: $siteId->toString(),
			provider: 'abc',
			provider_key: '456',
			display_name: 'ghi',
			details: '{}',
		);
	}

	public function testItWillFindFollowersForAnActor() {
		$site1 = $this->randomId(scrub: true);
		$site2 = $this->randomId(scrub: true);
		$allFollowers = [
			new Follower(siteId: $site1, provider: 'abc', providerKey: '987', displayName: 'A', details: ['p' => 'q']),
			new Follower(siteId: $site1, provider: 'xyz', providerKey: '987', displayName: 'B', details: ['w' => 'o']),
			new Follower(siteId: $site2, provider: 'xyz', providerKey: '987', displayName: 'C', details: ['r' => 's']),
		];
		$other = new Follower(siteId: $this->randomId(), provider: 'xyz', providerKey: '5', displayName: 'C', details: []);

		$this->db->table('followers')->insert(array_map(fn($f) => [
			'follower_uuid' => $f->id->toString(),
			'site_uuid' => $f->siteId->toString(),
			'provider' => $f->provider,
			'provider_key' => $f->providerKey,
			'display_name' => $f->displayName,
			'details' => json_encode($f->details),
		], $allFollowers, [$other]));

		$query = new FollowersByProviderAndKey(provider: 'xyz', providerKey: '987');
		$this->projection->onFollowersByProviderAndKey($query);
		$this->assertEquals(
			[$allFollowers[1], $allFollowers[2]],
			$query->results()
		);
	}

	public function testItWillFindFollowersForASite() {
		$site1 = $this->randomId(scrub: true);
		$allFollowers = [
			new Follower(siteId: $site1, provider: 'abc', providerKey: '123', displayName: 'A', details: ['p' => 'q']),
			new Follower(siteId: $site1, provider: 'xyz', providerKey: '987', displayName: 'B', details: ['w' => 'o']),
			new Follower(siteId: $site1, provider: 'xyz', providerKey: '654', displayName: 'C', details: ['r' => 's']),
		];
		$other = new Follower(siteId: $this->randomId(), provider: 'g', providerKey: '5', displayName: 'C', details: []);

		$this->db->table('followers')->insert(array_map(fn($f) => [
			'follower_uuid' => $f->id->toString(),
			'site_uuid' => $f->siteId->toString(),
			'provider' => $f->provider,
			'provider_key' => $f->providerKey,
			'display_name' => $f->displayName,
			'details' => json_encode($f->details),
		], $allFollowers, [$other]));

		$query = new GetFollowersForSiteByProvider(siteId: $site1);
		$this->projection->onFollowersForSite($query);
		$this->assertEquals(
			['abc' => [$allFollowers[0]], 'xyz' => [$allFollowers[1], $allFollowers[2]]],
			$query->results()
		);
	}
}
