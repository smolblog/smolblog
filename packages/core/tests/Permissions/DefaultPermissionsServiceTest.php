<?php

namespace Smolblog\Core\Permissions;

use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Test\AppTest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\Stub;
use Smolblog\Core\Model;
use Smolblog\Core\Site\Data\SiteUserRepo;
use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Core\User\InternalSystemUser;
use Smolblog\Core\User\User;
use Smolblog\Core\User\UserRepo;

final class DefaultPermissionsServiceTest extends AppTest {
	public const INCLUDED_MODELS = [Model::class];

	private UserRepo&Stub $userRepo;
	private SiteUserRepo&Stub $siteUserRepo;

	protected function createMockServices(): array {
		$this->userRepo = $this->createStub(UserRepo::class);
		$this->siteUserRepo = $this->createStub(SiteUserRepo::class);

		return [
			UserRepo::class => fn() => $this->userRepo,
			SiteUserRepo::class => fn() => $this->siteUserRepo,
			...parent::createMockServices(),
		];
	}

	public static function superAdminActions() {
		return [
			'create a site' => ['canCreateSite', false],
			'make another user a Super Admin' => ['canGrantUserSudo', false],
			'manage other users\' connections' => ['canManageOtherConnections', false],
		];
	}

	public static function siteAdminActions() {
		return [
			'edit other users\' content' => ['canEditAllContent', true],
			'manage site channels' => ['canManageChannels', true],
			'edit other users\' media' => ['canEditAllMedia', true],
			'give other users site permissions' => ['canManagePermissions', true],
			'manage site settings' => ['canManageSettings', true],
		];
	}

	public static function siteAuthorActions() {
		return [
			'create content' => ['canCreateContent', true],
			'upload media' => ['canUploadMedia', true],
			'push content to a channel' => ['canPushContent', true],
		];
	}

	public static function anyUserActions() {
		return [
			'register another user' => ['canRegisterUser', false],
		];
	}

	#[DataProvider('superAdminActions')]
	#[DataProvider('siteAdminActions')]
	#[DataProvider('siteAuthorActions')]
	#[DataProvider('anyUserActions')]
	#[TestDox('The internal system user can $_dataName')]
	public function testSmolbotCan($function, $isSiteAction) {
		$userId = UuidFactory::fromString(InternalSystemUser::ID);
		$siteId = $this->randomId();

		$this->userRepo->method('hasUserWithId')->with($this->uuidEquals($userId))->willReturn(false);
		$this->userRepo->method('userById')->with($this->uuidEquals($userId))->willReturn(null);
		$this->siteUserRepo->method('permissionsForUser')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(SitePermissionLevel::None);

		$params = $isSiteAction ? ['userId' => $userId, 'siteId' => $siteId] : ['userId' => $userId];

		$this->assertTrue(
			$this->app->container->get(DefaultPermissionsService::class)->{$function}(...$params),
		);
	}

	#[DataProvider('superAdminActions')]
	#[DataProvider('siteAdminActions')]
	#[DataProvider('siteAuthorActions')]
	#[DataProvider('anyUserActions')]
	#[TestDox('A super admin user can $_dataName')]
	public function testSuperAdminCan($function, $isSiteAction) {
		$userId = $this->randomId();
		$siteId = $this->randomId();

		$this->userRepo->method('hasUserWithId')->with($this->uuidEquals($userId))->willReturn(true);
		$this->userRepo->method('userById')->with($this->uuidEquals($userId))->willReturn(
			new User(
				id: $userId,
				key: 'test',
				displayName: 'TestUser',
				sudo: true,
			),
		);
		$this->siteUserRepo->method('permissionsForUser')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(SitePermissionLevel::None);

		$params = $isSiteAction ? ['userId' => $userId, 'siteId' => $siteId] : ['userId' => $userId];

		$this->assertTrue(
			$this->app->container->get(DefaultPermissionsService::class)->{$function}(...$params),
		);
	}

	#[DataProvider('superAdminActions')]
	#[TestDox('A site admin can NOT $_dataName')]
	public function testSiteAdminCannot($function, $isSiteAction) {
		$userId = UuidFactory::fromString(InternalSystemUser::ID);
		$siteId = $this->randomId();

		$this->userRepo->method('hasUserWithId')->with($this->uuidEquals($userId))->willReturn(true);
		$this->userRepo->method('userById')->with($this->uuidEquals($userId))->willReturn(
			new User(
				id: $userId,
				key: 'test',
				displayName: 'TestUser',
				sudo: false,
			),
		);
		$this->siteUserRepo->method('permissionsForUser')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(SitePermissionLevel::Admin);

		$params = $isSiteAction ? ['userId' => $userId, 'siteId' => $siteId] : ['userId' => $userId];

		$this->assertFalse(
			$this->app->container->get(DefaultPermissionsService::class)->{$function}(...$params),
		);
	}

	#[DataProvider('siteAdminActions')]
	#[DataProvider('siteAuthorActions')]
	#[DataProvider('anyUserActions')]
	#[TestDox('A site admin can $_dataName')]
	public function testSiteAdminCan($function, $isSiteAction) {
		$userId = UuidFactory::fromString(InternalSystemUser::ID);
		$siteId = $this->randomId();

		$this->userRepo->method('hasUserWithId')->with($this->uuidEquals($userId))->willReturn(true);
		$this->userRepo->method('userById')->with($this->uuidEquals($userId))->willReturn(
			new User(
				id: $userId,
				key: 'test',
				displayName: 'TestUser',
				sudo: false,
			),
		);
		$this->siteUserRepo->method('permissionsForUser')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(SitePermissionLevel::Admin);

		$params = $isSiteAction ? ['userId' => $userId, 'siteId' => $siteId] : ['userId' => $userId];

		$this->assertTrue(
			$this->app->container->get(DefaultPermissionsService::class)->{$function}(...$params),
		);
	}

	#[DataProvider('superAdminActions')]
	#[DataProvider('siteAdminActions')]
	#[TestDox('A site author can NOT $_dataName')]
	public function testSiteAuthorCannot($function, $isSiteAction) {
		$userId = UuidFactory::fromString(InternalSystemUser::ID);
		$siteId = $this->randomId();

		$this->userRepo->method('hasUserWithId')->with($this->uuidEquals($userId))->willReturn(true);
		$this->userRepo->method('userById')->with($this->uuidEquals($userId))->willReturn(
			new User(
				id: $userId,
				key: 'test',
				displayName: 'TestUser',
				sudo: false,
			),
		);
		$this->siteUserRepo->method('permissionsForUser')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(SitePermissionLevel::Author);

		$params = $isSiteAction ? ['userId' => $userId, 'siteId' => $siteId] : ['userId' => $userId];

		$this->assertFalse(
			$this->app->container->get(DefaultPermissionsService::class)->{$function}(...$params),
		);
	}

	#[DataProvider('siteAuthorActions')]
	#[DataProvider('anyUserActions')]
	#[TestDox('A site author can $_dataName')]
	public function testSiteAuthorCan($function, $isSiteAction) {
		$userId = UuidFactory::fromString(InternalSystemUser::ID);
		$siteId = $this->randomId();

		$this->userRepo->method('hasUserWithId')->with($this->uuidEquals($userId))->willReturn(true);
		$this->userRepo->method('userById')->with($this->uuidEquals($userId))->willReturn(
			new User(
				id: $userId,
				key: 'test',
				displayName: 'TestUser',
				sudo: false,
			),
		);
		$this->siteUserRepo->method('permissionsForUser')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(SitePermissionLevel::Author);

		$params = $isSiteAction ? ['userId' => $userId, 'siteId' => $siteId] : ['userId' => $userId];

		$this->assertTrue(
			$this->app->container->get(DefaultPermissionsService::class)->{$function}(...$params),
		);
	}

	#[DataProvider('superAdminActions')]
	#[DataProvider('siteAdminActions')]
	#[DataProvider('siteAuthorActions')]
	#[TestDox('An unassigned user can NOT $_dataName')]
	public function testUnassignedUserCannot($function, $isSiteAction) {
		$userId = UuidFactory::fromString(InternalSystemUser::ID);
		$siteId = $this->randomId();

		$this->userRepo->method('hasUserWithId')->with($this->uuidEquals($userId))->willReturn(true);
		$this->userRepo->method('userById')->with($this->uuidEquals($userId))->willReturn(
			new User(
				id: $userId,
				key: 'test',
				displayName: 'TestUser',
				sudo: false,
			),
		);
		$this->siteUserRepo->method('permissionsForUser')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(SitePermissionLevel::None);

		$params = $isSiteAction ? ['userId' => $userId, 'siteId' => $siteId] : ['userId' => $userId];

		$this->assertFalse(
			$this->app->container->get(DefaultPermissionsService::class)->{$function}(...$params),
		);
	}

	#[DataProvider('anyUserActions')]
	#[TestDox('An unassigned user can $_dataName')]
	public function testUnassignedUserCan($function, $isSiteAction) {
		$userId = UuidFactory::fromString(InternalSystemUser::ID);
		$siteId = $this->randomId();

		$this->userRepo->method('hasUserWithId')->with($this->uuidEquals($userId))->willReturn(true);
		$this->userRepo->method('userById')->with($this->uuidEquals($userId))->willReturn(
			new User(
				id: $userId,
				key: 'test',
				displayName: 'TestUser',
				sudo: false,
			),
		);
		$this->siteUserRepo->method('permissionsForUser')->with($this->uuidEquals($userId), $this->uuidEquals($siteId))->willReturn(SitePermissionLevel::None);

		$params = $isSiteAction ? ['userId' => $userId, 'siteId' => $siteId] : ['userId' => $userId];

		$this->assertTrue(
			$this->app->container->get(DefaultPermissionsService::class)->{$function}(...$params),
		);
	}
}
