<?php

namespace Smolblog\Core\Connector\Commands;

use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorConfiguration;
use Smolblog\Core\Connector\ConnectorInitData;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\NoRefreshKit;
use Smolblog\Core\Connector\Services\AuthRequestStateRepo;
use Smolblog\Core\Model;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\ModelTest;

abstract class AbstractMockConnector implements Connector {
	public static function getConfiguration(): ConnectorConfiguration {
		return new ConnectorConfiguration(key: 'testmock');
	}
}

class BeginAuthRequestCommandTest extends ModelTest {
	const INCLUDED_MODELS = [Model::class];

	private AuthRequestStateRepo & MockObject $stateRepo;

	protected function createMockServices(): array {
		$handler = $this->createStub(AbstractMockConnector::class);
		$handler->method('getInitializationData')->willReturnCallback(fn($cbUrl) => new ConnectorInitData(
			url: $cbUrl,
			state: '0ab41adf-ef37-4b51-bee3-d38bfb1b0b7a',
			info: ['smol' => 'blog'],
		));

		$this->stateRepo = $this->createMock(AuthRequestStateRepo::class);

		return [
			AbstractMockConnector::class => fn() => $handler,
			AuthRequestStateRepo::class => fn() => $this->stateRepo,
		];
	}

	public function testHappyPath() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$command = new BeginAuthRequest(
			provider: 'testmock',
			userId: $userId,
			callbackUrl: '//smol.blog/callback/testmock',
			returnToUrl: '//dashboard.smolblog.com/account/connections',
		);

		$this->stateRepo->expects($this->once())->method('saveAuthRequestState')->with(new AuthRequestState(
			key: '0ab41adf-ef37-4b51-bee3-d38bfb1b0b7a',
			userId: $userId,
			provider: 'testmock',
			info: ['smol' => 'blog'],
			returnToUrl: '//dashboard.smolblog.com/account/connections',
		));

		$redirectUrl = $this->app->execute($command);
		$this->assertEquals('//smol.blog/callback/testmock', $redirectUrl);
	}
}
