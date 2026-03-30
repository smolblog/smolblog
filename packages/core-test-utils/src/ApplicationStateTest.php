<?php

namespace Smolblog\Core\Test;

use Cavatappi\Test\AppTest;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\Stub;
use Smolblog\Core\Channel\Services\ChannelHandler;
use Smolblog\Core\Connection\Commands\BeginAuthRequest;
use Smolblog\Core\Connection\Commands\FinishAuthRequest;
use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Core\Connection\Entities\ConnectionInitData;
use Smolblog\Core\Connection\Services\ConnectionDataService;
use Smolblog\Core\Connection\Services\ConnectionHandler;
use Smolblog\Core\Media\Services\MediaHandler;
use Smolblog\Core\Site\Commands\CreateSite;
use Smolblog\Core\Site\Data\SiteRepo;
use Smolblog\Core\Site\Entities\Site;
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
			$this->assertValueObjectEquals($siteObject, $repo->siteById($siteObject->id));
		}

		return [
			'users' => $users,
			'sites' => $sites,
		];
	}
}
