<?php

namespace Smolblog\Api\Connector;

use Smolblog\Test\TestCase;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Core\Connector\Commands\LinkChannelToSite;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\EndpointTestToolkit;
use stdClass;

final class ChannelLinkTest extends TestCase {
	use EndpointTestToolkit;
	const ENDPOINT = ChannelLink::class;

	public function testItGivesBadRequestForIncorrectIds() {
		$this->expectException(BadRequest::class);

		$endpoint = new ChannelLink($this->createStub(MessageBus::class));
		$endpoint->run(
			userId: $this->randomId(),
			body: new ChannelLinkRequest(
				channelId: $this->randomId(),
				siteId: $this->randomId(),
			),
		);
	}

	public function testItRespondsToACorrectRequest() {
		$command = new LinkChannelToSite(
			userId: Identifier::fromString('f19854f0-8859-433e-80cc-562db9cc9a77'),
			channelId: Identifier::fromString('33fa1634-3b14-4156-a7ae-8cfa4721d0d9'),
			siteId: Identifier::fromString('18d40e95-62d4-40a8-88dc-fd4e2707b6cf'),
			canPull: false,
			canPush: true,
		);

		$bus = $this->mockBusExpects($command);
		$bus->method('fetch')->willReturn(new stdClass());

		$endpoint = new ChannelLink($bus);

		$endpoint->run(
			userId: Identifier::fromString('f19854f0-8859-433e-80cc-562db9cc9a77'),
			body: ChannelLinkRequest::fromArray([
				'channelId' => '33fa1634-3b14-4156-a7ae-8cfa4721d0d9',
				'siteId' => '18d40e95-62d4-40a8-88dc-fd4e2707b6cf',
				'push' => true,
			]),
		);
	}
}
