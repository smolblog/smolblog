<?php

namespace Smolblog\Core\Site;

use DateTimeImmutable;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\TestCase;

final class ExampleSiteEvent extends SiteEvent {
	public function __construct(
		public readonly string $one = 'two',
		mixed ...$args
	)
	{
		parent::__construct(...$args);
	}
	public function getPayload(): array { return ['one' => 'two']; }
}

final class SiteEventTest extends TestCase {
	public function testItWillSerializeCorrectly() {
		$object = new ExampleSiteEvent(
			siteId: Identifier::fromString('bb18639a-048f-40a0-adc3-6efc274b4080'),
			userId: Identifier::fromString('b9158407-3e19-48e8-8b6c-cc51bf657620'),
			id: Identifier::fromString('f2a47b7c-c35f-4899-8db7-e8fab982af1e'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);
		$array = [
			'type' => ExampleSiteEvent::class,
			'siteId' => 'bb18639a-048f-40a0-adc3-6efc274b4080',
			'userId' => 'b9158407-3e19-48e8-8b6c-cc51bf657620',
			'id' => 'f2a47b7c-c35f-4899-8db7-e8fab982af1e',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
			'payload' => [ 'one' => 'two' ],
		];

		$this->assertEquals($object, SiteEvent::fromTypedArray($array));
		$this->assertEquals($array, $object->toArray());
	}
}
