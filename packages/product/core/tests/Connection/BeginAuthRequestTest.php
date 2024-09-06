<?php

namespace Smolblog\Core\Connection\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\Core\Connection\Entities\ConnectionInitData;
use Smolblog\Foundation\Exceptions\ServiceNotRegistered;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\ConnectionTestBase;

class BeginAuthRequestTest extends ConnectionTestBase {
	public function testHappyPath() {
		$userId = Identifier::fromString('8de40399-240e-4e04-bfc5-a7a4bfeffdd5');
		$command = new BeginAuthRequest(
			provider: 'testmock',
			userId: $userId,
			callbackUrl: '//smol.blog/callback/testmock',
			returnToUrl: '//dashboard.smolblog.com/account/connections',
		);

		$this->handler->method('getInitializationData')->willReturnCallback(fn($cbUrl) => new ConnectionInitData(
			url: $cbUrl,
			state: '0ab41adf-ef37-4b51-bee3-d38bfb1b0b7a',
			info: ['smol' => 'blog'],
		));

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

	public function testProviderNotRegistered() {
		$this->expectException(ServiceNotRegistered::class);

		$this->app->execute(new BeginAuthRequest(
			provider: 'not registered',
			userId: Identifier::fromString('d18ba802-2a29-4c3e-b4db-d3dd7e6962de'),
			callbackUrl: '//smol.blog/callback/testmock',
		));
	}
}
