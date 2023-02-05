<?php

namespace Smolblog\Core\Connector\Events;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class ChannelSiteLinkSetTest extends TestCase {
	public function testItWillCorrectlyDeserialize() {
		$timestamp = new DateTimeImmutable('2022-02-22T22:22:22+0000');
		$id = Identifier::createFromDate($timestamp);
		$data = [
			'type' => ChannelSiteLinkSet::class,
			'connectionId' => 'a44f29ff-b55d-4fb5-94b6-230411fcf94c',
			'userId' => 'a866094a-c54f-49dc-baf5-e7da9985af23',
			'id' => $id->toString(),
			'timestamp' => $timestamp->format(DateTimeInterface::RFC3339_EXTENDED),
			'payload' => [
				'channelId' => '243c6efe-d3e7-4537-9203-8b5221bb971b',
				'siteId' => '40c96b2c-3bc3-4cc6-9ed3-c704df50b2f5',
				'canPush' => true,
				'canPull' => true,
			],
		];

		$expected = new ChannelSiteLinkSet(
			channelId: Identifier::fromString('243c6efe-d3e7-4537-9203-8b5221bb971b'),
			siteId: Identifier::fromString('40c96b2c-3bc3-4cc6-9ed3-c704df50b2f5'),
			canPull: true,
			canPush: true,
			connectionId: Identifier::fromString('a44f29ff-b55d-4fb5-94b6-230411fcf94c'),
			userId: Identifier::fromString('a866094a-c54f-49dc-baf5-e7da9985af23'),
			id: $id,
			timestamp: $timestamp,
		);

		$this->assertEquals($expected, ConnectorEvent::fromTypedArray($data));
	}
}
