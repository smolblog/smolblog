<?php

namespace Smolblog\Core\Channel\Entities;

use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Test\TestCase;

final class ChannelTest extends TestCase {
	public function testUnknownChannelDeserialization() {
		$serialized = [
			'type' => 'oddEvan\Unknown\Channel',
			'handler' => 'testmock',
			'handlerKey' => 'same',
			'displayName' => 'Same Channel',
			'userId' => $this->randomId()->toString(),
			'connectionId' => $this->randomId()->toString(),
			'authKey' => '123',
			'anotherField' => ['one', 'two', 'three'],
		];

		$actual = Channel::deserializeValue($serialized);
		$this->assertInstanceOf(BasicChannel::class, $actual);
		$this->assertEquals('123', $actual->details['authKey']);
		$this->assertEquals(['one', 'two', 'three'], $actual->details['anotherField']);
	}

	public function testEventCreatesEntryObject() {
		$content = new Content(
			body: new Note(new Markdown('Hello')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$entry = new ContentChannelEntry(
			contentId: $content->id,
			channelId: $this->randomId(),
			url: new Url('//smol.test/post'),
			details: ['dbid' => 42],
		);
		$event = new ContentPushedToChannel(
			content: $content,
			channelId: $entry->channelId,
			userId: $content->userId,
			aggregateId: $content->siteId,
			url: $entry->url,
			details: $entry->details,
		);

		$this->assertEquals($entry, $event->getEntryObject());
	}
}
