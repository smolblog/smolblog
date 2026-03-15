<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Fields\Markdown;
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
use Smolblog\Core\User\User;
use Smolblog\Core\User\UserGrantedSudo;
use Smolblog\Core\User\UserRegistered;
use Smolblog\CoreDataSql\Test\DataTestBase;

#[AllowMockObjectsWithoutExpectations]
final class UserProjectionTest extends DataTestBase {
	private SerializationService $serde;

	protected function setUp(): void {
		parent::setUp();
		$this->serde = $this->app->container->get(SerializationService::class);
	}

	public function testUserRegistered() {
		$projection = $this->app->container->get(UserProjection::class);

		$user = new User(
			id: $this->randomId(),
			key: 'test',
			displayName: 'Test Testerson',
			sudo: false,
		);
		$event = new UserRegistered(
			userId: $this->randomId(),
			entityId: $user->id,
			key: $user->key,
			displayName: $user->displayName,
		);

		$this->assertFalse($projection->hasUserWithId($user->id));
		$this->assertFalse($projection->hasUserWithKey($user->key));
		$this->assertNull($projection->userById($user->id));
		$this->app->dispatch($event);
		$this->assertTrue($projection->hasUserWithId($user->id));
		$this->assertTrue($projection->hasUserWithKey($user->key));
		$this->assertValueObjectEquals($user, $projection->userById($user->id));
	}

	public function testUserGrantedSudo() {
		$projection = $this->app->container->get(UserProjection::class);
		$env = $this->app->container->get(DatabaseEnvironment::class);
		$db = $env->getConnection();

		$oldUser = new User(
			id: $this->randomId(),
			key: 'test',
			displayName: 'Test Testerson',
			sudo: false,
		);
		$newUser = $oldUser->with(
			sudo: true,
		);
		$event = new UserGrantedSudo(
			userId: $this->randomId(),
			entityId: $oldUser->id,
		);

		$db->insert($env->tableName('users'), [
			'user_uuid' => $oldUser->id,
			'key' => $oldUser->key,
			'user_obj' => $this->serde->toJson($oldUser),
		]);
		$this->assertValueObjectEquals($oldUser, $projection->userById($oldUser->id));

		$this->app->dispatch($event);
		$this->assertValueObjectEquals($newUser, $projection->userById($newUser->id));
	}

	public function testItFailsSilentlyOnEditsIfContentDoesNotExist() {
		$projection = $this->app->container->get(UserProjection::class);

		$missingUser = new User(
			id: $this->randomId(),
			key: 'm',
			displayName: 'Missing No.',
			sudo: false,
		);
		$this->assertFalse($projection->hasUserWithId($missingUser->id));

		$projection->onUserGrantedSudo(
			new UserGrantedSudo(
				userId: $this->randomId(),
				entityId: $missingUser->id,
			),
		);
		$this->assertFalse($projection->hasUserWithId($missingUser->id));
	}
}
