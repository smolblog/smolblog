<?php

namespace Smolblog\CoreDataSql;

use Cavatappi\Infrastructure\Serialization\SerializationService;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\CoreDataSql\Test\DataTestBase;

#[AllowMockObjectsWithoutExpectations]
final class ScratchPadTest extends DataTestBase {
	private SerializationService $serde;

	protected function setUp(): void {
		parent::setUp();
		$this->serde = $this->app->container->get(SerializationService::class);
	}

	public function testAuthRequestState() {
		$projection = $this->app->container->get(ScratchPad::class);

		$state = new AuthRequestState(
			key: $this->randomId()->toString(),
			userId: $this->randomId(),
			handler: 'smolblog',
			info: ['email' => 'snek@smol.blog'],
			returnToUrl: 'https://dashboard.smol.blog/',
		);

		$this->assertNull($projection->getAuthRequestState($state->key));

		$projection->saveAuthRequestState($state);

		$this->assertValueObjectEquals($state, $projection->getAuthRequestState($state->key));
	}

	public function testItPurgesOldStatesAfterAccess() {
		$projection = $this->app->container->get(ScratchPad::class);
		$this->app->container->get(DatabaseService::class)
			->insert('scratch_pad', [
				'key' => 'AuthRequestState__expired',
				'value' => $this->serde->toJson(
					new AuthRequestState(
						key: 'expired',
						userId: $this->randomId(),
						handler: 'cavatappi',
						info: ['email' => 'bot@smol.blog'],
					),
				),
				'delete_after' => new DateTimeImmutable('2 hours ago')->format('Y-m-d H:i:s.u'),
			]);

		$this->assertNotNull($projection->getAuthRequestState('expired'));
		$this->assertNull($projection->getAuthRequestState('expired'));
	}
}
