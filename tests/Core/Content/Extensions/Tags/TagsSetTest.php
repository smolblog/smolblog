<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class TagsSetTest extends TestCase {
	public function testItGivesTheFullExtension() {
		$expected = new Tags(tags: [
			new Tag('one'),
			new Tag('two'),
		]);

		$event = new TagsSet(
			tagText:['one', 'two'],
			contentId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			siteId: Identifier::createRandom(),
		);

		$this->assertEquals($expected, $event->getNewExtension());
	}
}
