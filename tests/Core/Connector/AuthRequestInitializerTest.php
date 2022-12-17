<?php

namespace Smolblog\Core\Connector\Services;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Connector\Commands\BeginAuthRequest;
use Smolblog\Core\Connector\ConnectorInitData;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorRegistrar;
use Smolblog\Core\Connector\Entities\AuthRequestStateWriter;

final class AuthRequestInitializerTest extends TestCase {
	private AuthRequestInitializer $service;

	public function testItHandlesTheBeginAuthRequestCommand(): void {
		$authUrl = 'https://something.com/';

		$connector = $this->createMock(Connector::class);
		$connector->expects($this->once())
		          ->method('getInitializationData')
							->willReturn(new ConnectorInitData(url: $authUrl, state: 'bob', info: []));

		$connectors = $this->createMock(ConnectorRegistrar::class);
		$connectors->expects($this->once())
							 ->method('get')
							 ->willReturn($connector);

		$stateSaver = $this->createMock(AuthRequestStateWriter::class);
		$stateSaver->expects($this->once())->method('save');

		$this->service = new AuthRequestInitializer(
			connectors: $connectors,
			stateSaver: $stateSaver,
		);

		$command = new BeginAuthRequest(userId: 1, provider: 'smol', callbackUrl: '//smol.blog');
		$response = $this->service->run($command);

		$this->assertEquals($authUrl, $response);
	}
}
