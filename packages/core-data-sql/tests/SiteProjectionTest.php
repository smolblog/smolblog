<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Fields\Markdown;
use Cavatappi\Foundation\Reflection\DisplayName;
use Cavatappi\Infrastructure\Serialization\SerializationService;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\ContentCanonicalUrlSet;
use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Site\Entities\Site;
use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Core\Site\Events\SiteCreated;
use Smolblog\Core\Site\Events\SiteDetailsUpdated;
use Smolblog\Core\Site\Events\UserSitePermissionsSet;
use Smolblog\Core\User\User;
use Smolblog\Core\User\UserGrantedSudo;
use Smolblog\Core\User\UserRegistered;
use Smolblog\CoreDataSql\Test\DataTestBase;

#[AllowMockObjectsWithoutExpectations]
final class SiteProjectionTest extends DataTestBase {
	private SerializationService $serde;

	protected function setUp(): void {
		parent::setUp();
		$this->serde = $this->app->container->get(SerializationService::class);
	}

	public function testSiteCreated() {
		$projection = $this->app->container->get(SiteProjection::class);

		$site = new Site(
			id: $this->randomId(),
			key: 'test',
			displayName: 'Test Site',
			userId: $this->randomId(),
			description: 'A test site',
			pictureId: $this->randomId(),
		);
		$event = new SiteCreated(
			userId: $site->userId,
			aggregateId: $site->id,
			key: $site->key,
			displayName: $site->displayName,
			description: $site->description,
			pictureId: $site->pictureId,
		);

		$this->assertValueObjectEquals($site, $event->getSiteObject());

		$this->assertFalse($projection->hasSiteWithId($site->id));
		$this->assertFalse($projection->hasSiteWithKey($site->key));
		$this->assertNull($projection->siteById($site->id));
		$this->assertEmpty($projection->sitesForUser($site->userId));

		$this->app->dispatch($event);

		$this->assertTrue($projection->hasSiteWithId($site->id));
		$this->assertTrue($projection->hasSiteWithKey($site->key));
		$this->assertValueObjectEquals($site, $projection->siteById($site->id));
		$this->assertNotEmpty($projection->sitesForUser($site->userId));

		// User should also be set up.
		$this->assertTrue($projection->hasUserForSite($site->userId, $site->id));
		$this->assertEquals(SitePermissionLevel::Admin, $projection->permissionsForUser($site->userId, $site->id));
		$this->assertNotEmpty($projection->userIdsForSite($site->id));

		// Random user should not be set up.
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($this->randomId(), $site->id));
	}

	public function testSiteDetailsUpdated() {
		$projection = $this->app->container->get(SiteProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$oldSite = new Site(
			id: $this->randomId(),
			key: 'test',
			displayName: 'Test Testerson',
			userId: $this->randomId(),
			description: 'A bad description',
			pictureId: $this->randomId(),
		);
		$newSiteOne = $oldSite->with(
			description: 'A better description',
		);
		$newSiteTwo = $newSiteOne->with(
			displayName: 'Test Site Two',
		);
		$newSiteThree = $newSiteTwo->with(
			pictureId: $this->randomId(),
		);

		$eventOne = new SiteDetailsUpdated(
			userId: $oldSite->userId,
			aggregateId: $oldSite->id,
			description: 'A better description',
		);
		$eventTwo = new SiteDetailsUpdated(
			userId: $oldSite->userId,
			aggregateId: $oldSite->id,
			displayName: 'Test Site Two',
		);
		$eventThree = new SiteDetailsUpdated(
			userId: $oldSite->userId,
			aggregateId: $oldSite->id,
			pictureId: $newSiteThree->pictureId,
		);

		$db->insert($env->tableName('sites'), [
			'site_uuid' => $oldSite->id,
			'user_uuid' => $oldSite->userId,
			'key' => $oldSite->key,
			'site_obj' => $this->serde->toJson($oldSite),
		]);
		$this->assertValueObjectEquals($oldSite, $projection->siteById($oldSite->id));

		$this->app->dispatch($eventOne);
		$this->assertValueObjectEquals($newSiteOne, $projection->siteById($oldSite->id));
		$this->app->dispatch($eventTwo);
		$this->assertValueObjectEquals($newSiteTwo, $projection->siteById($oldSite->id));
		$this->app->dispatch($eventThree);
		$this->assertValueObjectEquals($newSiteThree, $projection->siteById($oldSite->id));
	}

	public function testUserSitePermissionsSet() {
		$userIdOne = $this->randomId();
		$siteIdOne = $this->randomId();
		$userIdTwo = $this->randomId();
		$siteIdTwo = $this->randomId();
		$projection = $this->app->container->get(SiteProjection::class);

		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdOne, $siteIdOne));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdOne, $siteIdTwo));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdTwo, $siteIdOne));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdTwo, $siteIdTwo));

		$this->app->dispatch(
			new UserSitePermissionsSet(
				userId: $this->randomId(),
				aggregateId: $siteIdOne,
				entityId: $userIdOne,
				level: SitePermissionLevel::Author,
			),
		);
		$this->app->dispatch(
			new UserSitePermissionsSet(
				userId: $this->randomId(),
				aggregateId: $siteIdTwo,
				entityId: $userIdTwo,
				level: SitePermissionLevel::Author,
			),
		);
		$this->assertEquals(SitePermissionLevel::Author, $projection->permissionsForUser($userIdOne, $siteIdOne));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdOne, $siteIdTwo));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdTwo, $siteIdOne));
		$this->assertEquals(SitePermissionLevel::Author, $projection->permissionsForUser($userIdTwo, $siteIdTwo));

		$this->app->dispatch(
			new UserSitePermissionsSet(
				userId: $this->randomId(),
				aggregateId: $siteIdOne,
				entityId: $userIdOne,
				level: SitePermissionLevel::None,
			),
		);
		$this->app->dispatch(
			new UserSitePermissionsSet(
				userId: $this->randomId(),
				aggregateId: $siteIdTwo,
				entityId: $userIdTwo,
				level: SitePermissionLevel::None,
			),
		);
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdOne, $siteIdOne));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdOne, $siteIdTwo));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdTwo, $siteIdOne));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdTwo, $siteIdTwo));

		$this->app->dispatch(
			new UserSitePermissionsSet(
				userId: $this->randomId(),
				aggregateId: $siteIdOne,
				entityId: $userIdOne,
				level: SitePermissionLevel::Admin,
			),
		);
		$this->app->dispatch(
			new UserSitePermissionsSet(
				userId: $this->randomId(),
				aggregateId: $siteIdTwo,
				entityId: $userIdTwo,
				level: SitePermissionLevel::Admin,
			),
		);
		$this->assertEquals(SitePermissionLevel::Admin, $projection->permissionsForUser($userIdOne, $siteIdOne));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdOne, $siteIdTwo));
		$this->assertEquals(SitePermissionLevel::None, $projection->permissionsForUser($userIdTwo, $siteIdOne));
		$this->assertEquals(SitePermissionLevel::Admin, $projection->permissionsForUser($userIdTwo, $siteIdTwo));
	}
}
