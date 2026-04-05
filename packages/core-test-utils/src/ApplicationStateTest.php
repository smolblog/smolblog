<?php

namespace Smolblog\Core\Test;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Fields\Markdown;
use Cavatappi\Test\AppTest;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\Stub;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Services\ChannelHandler;
use Smolblog\Core\Connection\Commands\BeginAuthRequest;
use Smolblog\Core\Connection\Commands\FinishAuthRequest;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Entities\ConnectionInitData;
use Smolblog\Core\Connection\Services\ConnectionDataService;
use Smolblog\Core\Connection\Services\ConnectionHandler;
use Smolblog\Core\Content\Commands\CreateContent;
use Smolblog\Core\Content\Commands\DeleteContent;
use Smolblog\Core\Content\Commands\UpdateContent;
use Smolblog\Core\Content\Data\ContentRepo;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Extensions\License\License;
use Smolblog\Core\Content\Extensions\License\LicenseType;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Services\ContentDataService;
use Smolblog\Core\Content\Types\Article\Article;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Picture\Picture;
use Smolblog\Core\Content\Types\Reblog\Reblog;
use Smolblog\Core\Media\Services\MediaHandler;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Commands\CreateSite;
use Smolblog\Core\Site\Data\SiteRepo;
use Smolblog\Core\Site\Data\SiteUserRepo;
use Smolblog\Core\Site\Entities\Site;
use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Core\Test\Stubs\ChannelHandlerTestBase;
use Smolblog\Core\Test\Stubs\ConnectionHandlerTestBase;
use Smolblog\Core\Test\Stubs\MediaHandlerTestBase;
use Smolblog\Core\User\GrantUserSudo;
use Smolblog\Core\User\InternalSystemUser;
use Smolblog\Core\User\RegisterUser;
use Smolblog\Core\User\User;
use Smolblog\Core\User\UserRepo;

abstract class ApplicationStateTest extends AppTest {
	protected ChannelHandler&Stub $channelHandler;
	protected ConnectionHandler&Stub $connectionHandler;
	protected MediaHandler&Stub $mediaHandler;

	// TODO: move this to the framework
	public static function assertUuidNotEquals(UuidInterface $expected, UuidInterface $actual, string $message = ''): void {
		self::assertThat($actual, self::logicalNot(self::uuidEquals($expected)), $message);
	}

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
	}

	protected function setUp(): void {
		parent::setUp();
	}

	protected function createMockServices(): array {
		$this->channelHandler = $this->createStub(ChannelHandlerTestBase::class);
		$this->connectionHandler = $this->createStub(ConnectionHandlerTestBase::class);
		$this->mediaHandler = $this->createStub(MediaHandlerTestBase::class);

		return [
			...parent::createMockServices(),
			ChannelHandlerTestBase::class => fn() => $this->channelHandler,
			ConnectionHandlerTestBase::class => fn() => $this->connectionHandler,
			MediaHandlerTestBase::class => fn() => $this->mediaHandler,
		];
	}

	#[TestDox('All dependencies are met or stubbed.')]
	final public function testCompleteApp() {
		$this->assertCompleteDependencyMap();
	}

	#[TestDox('Users can be created and retrieved.')]
	final public function testUserCreation() {
		$repo = $this->app->container->get(UserRepo::class);

		$windId = $this->app->execute(
			new RegisterUser(
				userId: InternalSystemUser::object()->id,
				key: 'windfox',
				displayName: 'Ronyo Gwaeron',
			),
		);
		$this->app->execute(
			new GrantUserSudo(
				userId: InternalSystemUser::object()->id,
				userIdToEscalate: $windId,
			),
		);
		$this->assertTrue($repo->hasUserWithId($windId));
		$this->assertTrue($repo->hasUserWithkey('windfox'));

		$redId = $this->app->execute(
			new RegisterUser(
				userId: $windId,
				key: 'red',
				displayName: 'Eutychia of Mesone',
			),
		);
		$greenId = $this->app->execute(
			new RegisterUser(
				userId: $windId,
				key: 'green',
				displayName: 'Alec Squallchaser',
			),
		);
		$blueId = $this->app->execute(
			new RegisterUser(
				userId: $windId,
				key: 'blue',
				displayName: 'Jordan Hendrick',
			),
		);

		$users = [
			'windfox' => new User(
				id: $windId,
				key: 'windfox',
				displayName: 'Ronyo Gwaeron',
				sudo: true,
			),
			'red' => new User(
				id: $redId,
				key: 'red',
				displayName: 'Eutychia of Mesone',
				sudo: false,
			),
			'green' => new User(
				id: $greenId,
				key: 'green',
				displayName: 'Alec Squallchaser',
				sudo: false,
			),
			'blue' => new User(
				id: $blueId,
				key: 'blue',
				displayName: 'Jordan Hendrick',
				sudo: false,
			),
		];

		foreach ($users as $userObject) {
			$this->assertValueObjectEquals($userObject, $repo->userById($userObject->id));
		}

		return $users;
	}

	#[Depends('testUserCreation')]
	#[TestDox('Users can create connections to external services.')]
	final public function testConnections($users) {
		extract($users);

		$windfoxConnectStart = new ConnectionInitData(
			url: '//test.mock/oidc?requestId=' . $this->randomId(),
			state: $this->randomId()->toString(),
			info: [],
		);
		$blueConnectStart = new ConnectionInitData(
			url: '//test.mock/oidc?requestId=' . $this->randomId(),
			state: $this->randomId()->toString(),
			info: [],
		);
		$this->connectionHandler->method('getInitializationData')->willReturn(
			$windfoxConnectStart,
			$blueConnectStart,
		);

		$windfoxConnectionFinal = new Connection(
			userId: $windfox->id,
			handler: 'testmock',
			handlerKey: $this->randomId(),
			displayName: $windfox->displayName,
			details: ['id' => $windfox->id->toString()],
		);
		$blueConnectionFinal = new Connection(
			userId: $blue->id,
			handler: 'testmock',
			handlerKey: $this->randomId(),
			displayName: $blue->displayName,
			details: ['id' => $blue->id->toString()],
		);
		$this->connectionHandler->method('createConnection')->willReturn(
			$blueConnectionFinal, // Yes, Blue first!
			$windfoxConnectionFinal,
		);

		$windfoxInitResult = $this->app->execute(
			new BeginAuthRequest(
				handler: 'testmock',
				userId: $windfox->id,
				callbackUrl: '//smol.blog/return',
				returnToUrl: '//dashboard.smol.blog/windfox',
			),
		);
		$this->assertEquals($windfoxConnectStart->url, $windfoxInitResult);

		$blueInitResult = $this->app->execute(
			new BeginAuthRequest(
				handler: 'testmock',
				userId: $blue->id,
				callbackUrl: '//smol.blog/return',
				returnToUrl: '//dashboard.smol.blog/blue',
			),
		);
		$this->assertEquals($blueConnectStart->url, $blueInitResult);

		// Intentionally swapping the order here!
		$blueRedirectUrl = $this->app->execute(
			new FinishAuthRequest(
				handler: 'testmock',
				stateKey: $blueConnectStart->state,
				code: $this->randomId(),
			),
		);
		$this->assertEquals('//dashboard.smol.blog/blue', $blueRedirectUrl);

		$windfoxRedirectUrl = $this->app->execute(
			new FinishAuthRequest(
				handler: 'testmock',
				stateKey: $windfoxConnectStart->state,
				code: $this->randomId(),
			),
		);
		$this->assertEquals('//dashboard.smol.blog/windfox', $windfoxRedirectUrl);

		$connections = $this->app->container->get(ConnectionDataService::class);
		$windfoxConnections = $connections->connectionsForUser($windfox->id, $windfox->id);
		$blueConnections = $connections->connectionsForUser($blue->id, $blue->id);

		$this->assertValueObjectEquals($windfoxConnectionFinal, $windfoxConnections[0]);
		$this->assertValueObjectEquals($blueConnectionFinal, $blueConnections[0]);

		// Test read permissions
		$this->assertEmpty($connections->connectionsForUser($windfox->id, $blue->id));
		$this->assertValueObjectEquals($blueConnectionFinal, $connections->connectionsForUser($blue->id, $windfox->id)[0]);
	}

	#[Depends('testUserCreation')]
	#[TestDox('Users can create and manage sites.')]
	final public function testSites($users) {
		extract($users);
		$repo = $this->app->container->get(SiteRepo::class);
		$perms = $this->app->container->get(SitePermissionsService::class);

		$windId = $this->app->execute(new CreateSite(
			userId: $windfox->id,
			key: 'windfox',
			displayName: 'Ronyo the Windfox',
			description: 'Climbing mountains and running through fields.',
			siteUser: $windfox->id,
		));
		$redId = $this->app->execute(new CreateSite(
			userId: $windfox->id,
			key: 'red',
			displayName: 'Euty got dat booty',
			siteUser: $red->id,
			description: 'Play nice or you\'ll meet the hooves.',
		));
		$greenId = $this->app->execute(new CreateSite(
			userId: $windfox->id,
			key: 'green',
			displayName: 'Call Me Al(ec)',
			siteUser: $green->id,
			description: 'You can\'t pronounce my name, so call me Alec.',
		));
		$blueId = $this->app->execute(new CreateSite(
			userId: $windfox->id,
			key: 'blue',
			displayName: 'Jordan\'s stormy banks',
			siteUser: $blue->id,
			description: 'DFTBA, I\'ll see you on Thursday.',
		));

		$sites = [
			'windfox' => new Site(
				id: $windId,
				key: 'windfox',
				displayName: 'Ronyo the Windfox',
				userId: $windfox->id,
				description: 'Climbing mountains and running through fields.',
			),
			'red' => new Site(
				id: $redId,
				key: 'red',
				displayName: 'Euty got dat booty',
				userId: $red->id,
				description: 'Play nice or you\'ll meet the hooves.',
			),
			'green' => new Site(
				id: $greenId,
				key: 'green',
				displayName: 'Call Me Al(ec)',
				userId: $green->id,
				description: 'You can\'t pronounce my name, so call me Alec.',
			),
			'blue' => new Site(
				id: $blueId,
				key: 'blue',
				displayName: 'Jordan\'s stormy banks',
				userId: $blue->id,
				description: 'DFTBA, I\'ll see you on Thursday.',
			),
		];

		foreach ($sites as $siteObject) {
			$this->assertValueObjectEquals(
				$siteObject,
				$repo->siteById($siteObject->id),
				"Could not retrieve site {$siteObject->key}",
			);

			$actualUsers = array_map('\\strval', $repo->userIdsForSite($siteObject->id));
			$this->assertEquals(
				[strval($siteObject->userId)],
				$actualUsers,
				"Incorrect users for site {$siteObject->key}",
			);

			$expectedUserPerms = array_map(fn($u) => match ($u->key) {
				'windfox' => true,
				$siteObject->key => true,
				default => false,
			}, $users);
			$actualUserPerms = array_map(fn($u) => $perms->canManageSettings($u->id, $siteObject->id), $users);
			$this->assertEquals($expectedUserPerms, $actualUserPerms);
		}

		return [
			'users' => $users,
			'sites' => $sites,
		];
	}

	#[Depends('testSites')]
	#[TestDox('Users can create and manage content.')]
	final public function testContent($fixtures) {
		extract($fixtures);

		$keys = ['windfox', 'red', 'green', 'blue'];
		$bodies = [
			'note' => new Note(new Markdown('This is a test note.')),
			'reblog' => new Reblog(
				url: HttpMessageFactory::uri('https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
			),
			'article' => new Article(
				title: 'A longer article',
				text: new Markdown("Just kidding, I can't write that much.\n\nButts."),
			),
		];

		$repo = $this->app->container->get(ContentDataService::class);

		foreach ($keys as $key) {
			$userId = $users[$key]->id;
			$siteId = $sites[$key]->id;

			$sudo = $users['windfox'];
			$other = $key === 'blue' ? $users['red'] : $users['blue'];

			foreach ($bodies as $type => $body) {
				$contentId = $this->app->execute(
					new CreateContent(
						userId: $userId,
						body: $body,
						siteId: $siteId,
					),
				);
				$content = new Content(
					body: $body,
					siteId: $siteId,
					userId: $userId,
					id: $contentId,
				);

				$this->assertValueObjectEquals(
					$content,
					$repo->contentById($contentId, $userId),
					"Failed to retrieve {$type} for {$key}",
				);
				$this->assertValueObjectEquals(
					$content,
					$repo->contentById($contentId, $sudo->id),
					"Failed to retrieve {$key} {$type} for superuser {$sudo->key}",
				);
				$this->assertNull(
					$repo->contentById($contentId, $other->id),
					"Content {$type} for {$key} not null for {$other->key}",
				);

				$this->app->execute(
					new UpdateContent(
						contentId: $contentId,
						userId: $sudo->id,
						body: $content->body,
						siteId: $siteId,
						contentUserId: $userId,
						extensions: [
							new Tags(['test','usual']),
						],
					),
				);
				$this->app->execute(
					new UpdateContent(
						contentId: $contentId,
						userId: $userId,
						body: $content->body,
						siteId: $siteId,
						contentUserId: $userId,
						extensions: [
							new Tags(['test','usual']),
							new License(originalWork: true, baseType: LicenseType::Attribution),
						],
					),
				);

				$content = $content->with(
					extensions: [
						new Tags(['test','usual']),
						new License(originalWork: true, baseType: LicenseType::Attribution),
					],
				);
				$this->assertValueObjectEquals(
					$content,
					$repo->contentById($contentId, $userId),
					"Failed to retrieve edited {$type} for {$key}",
				);

				try {
					$this->app->execute(
						new DeleteContent(
							userId: $other->id,
							contentId: $contentId,
						),
					);
					$this->assertTrue(false, "User {$other->key} deleted content in {$key}");
				} catch (CommandNotAuthorized $e) {
					// Do nothing; this is good!
				}
			}
		}
	}
}
