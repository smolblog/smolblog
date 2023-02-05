<?php

namespace Smolblog\Core\Content\Events;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Identifier;

final class ContentVisibilityChangedTest extends TestCase {
	public function testItWillSerializeCorrectly() {
		$expected = [
			'type' => ContentVisibilityChanged::class,
			'payload' => ['visibility' => 'protected'],
			'contentId' => 'e08231e7-e73e-4f4a-a940-1010b08335d3',
			'userId' => 'ccc51971-4219-44af-96be-d36b7d7c3cff',
			'siteId' => 'b4be397a-e5ea-427d-8794-cc5e209141bf',
			'id' => '55c80dab-ff1f-4c6b-b6e3-d042bb87ddb6',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
		];

		$actual = new ContentVisibilityChanged(
			visibility: ContentVisibility::Protected,
			contentId: Identifier::fromString('e08231e7-e73e-4f4a-a940-1010b08335d3'),
			userId: Identifier::fromString('ccc51971-4219-44af-96be-d36b7d7c3cff'),
			siteId: Identifier::fromString('b4be397a-e5ea-427d-8794-cc5e209141bf'),
			id: Identifier::fromString('55c80dab-ff1f-4c6b-b6e3-d042bb87ddb6'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$this->assertEquals($expected, $actual->toArray());
	}

	public function testItWillDeserializeCorrectly() {
		$expected = new ContentVisibilityChanged(
			visibility: ContentVisibility::Protected,
			contentId: Identifier::fromString('e08231e7-e73e-4f4a-a940-1010b08335d3'),
			userId: Identifier::fromString('ccc51971-4219-44af-96be-d36b7d7c3cff'),
			siteId: Identifier::fromString('b4be397a-e5ea-427d-8794-cc5e209141bf'),
			id: Identifier::fromString('55c80dab-ff1f-4c6b-b6e3-d042bb87ddb6'),
			timestamp: new DateTimeImmutable('2022-02-22 22:22:22'),
		);

		$actual = [
			'type' => ContentVisibilityChanged::class,
			'payload' => ['visibility' => 'protected'],
			'contentId' => 'e08231e7-e73e-4f4a-a940-1010b08335d3',
			'userId' => 'ccc51971-4219-44af-96be-d36b7d7c3cff',
			'siteId' => 'b4be397a-e5ea-427d-8794-cc5e209141bf',
			'id' => '55c80dab-ff1f-4c6b-b6e3-d042bb87ddb6',
			'timestamp' => '2022-02-22T22:22:22.000+00:00',
		];

		$this->assertEquals($expected, ContentEvent::fromTypedArray($actual));
	}
}
