<?php

namespace Smolblog\Core\Content\Commands;

use DateTimeImmutable;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Objects\Identifier;

final class EditContentBaseAttributesTest extends TestCase {
	public function testItCanBeCreatedWithAtLeastOneValidAttribute() {
		$baseAtts = [
			'userId' => $this->randomId(),
			'siteId' => $this->randomId(),
			'contentId' => $this->randomId(),
		];

		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(...$baseAtts, permalink: '/thing/slug-23'),
		);
		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(...$baseAtts, publishTimestamp: new DateTimeImmutable()),
		);
		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(...$baseAtts, authorId: $this->randomId()),
		);

		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(
				...$baseAtts,
				permalink: '/thing/slug-23',
				publishTimestamp: new DateTimeImmutable()
			),
		);
		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(
				...$baseAtts,
				publishTimestamp: new DateTimeImmutable(),
				authorId: $this->randomId()
			),
		);
		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(
				...$baseAtts,
				permalink: '/thing/slug-23',
				authorId: $this->randomId()
			),
		);

		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(
				...$baseAtts,
				permalink: '/thing/slug-23',
				publishTimestamp: new DateTimeImmutable(),
				authorId: $this->randomId()
			),
		);
	}

	public function testItFailsIfNoAttributesAreProvided() {
		$this->expectException(InvalidCommandParametersException::class);

		new EditContentBaseAttributes(
			userId: $this->randomId(),
			siteId: $this->randomId(),
			contentId: $this->randomId(),
		);
	}
}
