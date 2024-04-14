<?php

namespace Smolblog\Test\Kits;

use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\EndpointConfig;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Service\Messaging\MessageBus;

trait EndpointTestToolkit {
	private function getApiEnvironment(): ApiEnvironment {
		return new class implements ApiEnvironment {
			public function getApiUrl(string $endpoint = '/'): string {
				return '//smol.blog/api' . $endpoint;
			}
		};
	}

	private function mockBusExpects(Command $expected) {
		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->equalTo($expected));
		return $bus;
	}

	public function testItGivesAValidConfiguration(): void {
		$config = self::ENDPOINT::getConfiguration();
		$this->assertInstanceOf(EndpointConfig::class, $config);
	}
}
