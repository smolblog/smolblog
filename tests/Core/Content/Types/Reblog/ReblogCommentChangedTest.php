<?php

namespace Smolblog\Core\Content\Types\Reblog;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

class ReblogCommentChangedTest extends TestCase {
	public function testTheInfoAndRenderCanBeSetAfterConstruction() {
		$event = new ReblogCommentChanged(
			comment: 'Hello',
			contentId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			siteId: Identifier::createRandom(),
		);

		$event->setInfo(new ExternalContentInfo(title: 'Do not', embed: '<iframe></iframe>'));

		$this->assertEquals('Hello', $event->getMarkdown());
		$event->setHtml('<p>Hello</p>');

		$this->assertEquals("<iframe></iframe>\n\n<p>Hello</p>", $event->getNewBody());
	}
}
