<?php

namespace Smolblog\Core\Channel\Entities;

use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Fields\Markdown;
use Cavatappi\Test\TestCase;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Types\Note\Note;

final class ChannelTest extends TestCase {
	public function testEventCreatesEntryObject() {
		$content = new Content(
			body: new Note(new Markdown('Hello')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $this->randomId(),
		);
		$entry = new ContentChannelEntry(
			contentId: $content->id,
			channelId: $this->randomId(),
			url: HttpMessageFactory::uri('//smol.test/post'),
			details: ['dbid' => 42],
		);
		$event = new ContentPushSucceeded(
			contentId: $content->id,
			channelId: $entry->channelId,
			userId: $content->userId,
			aggregateId: $content->siteId,
			processId: $this->randomId(),
			url: $entry->url,
			details: $entry->details,
		);

		$this->assertValueObjectEquals($entry, $event->getEntryObject());
	}
}
