<?php

namespace Smolblog\Core\Content\Events;

use DateTimeImmutable;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\GenericContent;
use Smolblog\Test\TestCase;

final class SamplePublicContentEvent extends PublicContentEvent {}

final class PublicContentEventTest extends TestCase {
	public function testContentStateCanBeSetAndRetrieved() {
		$content = new GenericContent(
			title: 'Hello world!',
			body: "<p>What's going on?</p>",
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/test/content.html',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
			id: $this->randomId(),
		);

		$event = new SamplePublicContentEvent(
			contentId: $content->id,
			userId: $content->authorId,
			siteId: $content->siteId,
		);
		$event->setContent($content);

		$this->assertEquals($content, $event->getContent());
	}
}
