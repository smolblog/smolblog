<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class TagsSetTest extends TestCase {
	public function testItGivesTheFullExtension() {
		$expected = new Tags(tags: [
			new Tag('one'),
			new Tag('two'),
		]);

		$event = new TagsSet(
			tagText:['one', 'two'],
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->assertEquals($expected, $event->getNewExtension());
	}
}
