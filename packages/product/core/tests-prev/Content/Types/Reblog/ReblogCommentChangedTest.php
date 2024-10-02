<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Test\TestCase;
use Smolblog\Foundation\Value\Fields\Identifier;

class ReblogCommentChangedTest extends TestCase {
	public function testTheInfoAndRenderCanBeSetAfterConstruction() {
		$event = new ReblogCommentChanged(
			comment: 'Hello',
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$event->setInfo(new ExternalContentInfo(title: 'Do not', embed: '<iframe></iframe>'));

		$this->assertEquals(['Hello'], $event->getMarkdown());
		$event->setMarkdownHtml(['<p>Hello</p>']);

		$this->assertEquals("<iframe></iframe>\n\n<p>Hello</p>", $event->getNewBody());
	}
}
