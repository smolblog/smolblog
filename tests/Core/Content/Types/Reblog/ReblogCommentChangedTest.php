<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

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
