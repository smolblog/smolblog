<?php

namespace Smolblog\Core\Content\Commands;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Framework\Objects\Identifier;

final class EditContentBaseAttributesTest extends TestCase {
	public function testItCanBeCreatedWithAtLeastOneValidAttribute() {
		$baseAtts = [
			'userId' => Identifier::createRandom(),
			'siteId' => Identifier::createRandom(),
			'contentId' => Identifier::createRandom(),
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
			new EditContentBaseAttributes(...$baseAtts, authorId: Identifier::createRandom()),
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
				authorId: Identifier::createRandom()
			),
		);
		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(
				...$baseAtts,
				permalink: '/thing/slug-23',
				authorId: Identifier::createRandom()
			),
		);

		$this->assertInstanceOf(
			EditContentBaseAttributes::class,
			new EditContentBaseAttributes(
				...$baseAtts,
				permalink: '/thing/slug-23',
				publishTimestamp: new DateTimeImmutable(),
				authorId: Identifier::createRandom()
			),
		);
	}

	public function testItFailsIfNoAttributesAreProvided() {
		$this->expectException(InvalidCommandParametersException::class);

		new EditContentBaseAttributes(
			userId: Identifier::createRandom(),
			siteId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
		);
	}
}
