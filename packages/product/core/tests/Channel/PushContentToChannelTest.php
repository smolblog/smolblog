<?php

namespace Smolblog\Core\Channel\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushFailed;
use Smolblog\Core\Channel\Events\ContentPushStarted;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Channel\Services\ContentPushException;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\ChannelTestBase;

final class PushContentToChannelTest extends ChannelTestBase {
	public function testHappyPathWithCustom() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'testmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$entry = new ContentChannelEntry(
			contentId: $content->id,
			channelId: $channel->getId(),
			url: 'https://test.smol.blog/post/test',
			details: [ 'post_id' => '12345' ],
		);
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->getId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->expectEvent(new ContentPushStarted(
			contentId: $content->id,
			channelId: $channel->getId(),
			userId: $content->userId,
			entityId: $entry->getId(),
			aggregateId: $content->siteId,
		));

		$this->handlerMock->expects($this->once())->method('pushContentToChannel')->with(
			content: $content,
			channel: $channel,
			userId: $content->userId,
			startEventId: $this->isInstanceOf(Identifier::class),
		);

		$this->app->execute($command);
	}

	public function testHappyPathWithDefault() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'defaultmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$entry = new ContentChannelEntry(
			contentId: $content->id,
			channelId: $channel->getId(),
			url: 'https://test.smol.blog/post/test',
			details: [ 'post_id' => '12345' ],
		);
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->getId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->defaultHandlerMock->method('push')->willReturn($entry);

		$this->expectEvents([
			new ContentPushStarted(
				contentId: $content->id,
				channelId: $channel->getId(),
				userId: $content->userId,
				entityId: $entry->getId(),
				aggregateId: $content->siteId,
			),
			new ContentPushSucceeded(
				contentId: $content->id,
				channelId: $channel->getId(),
				startEventId: $this->defaultHandlerMock->startEventId, // Use known ID from the mock class.
				userId: $content->userId,
				entityId: $entry->getId(),
				aggregateId: $content->siteId,
				url: 'https://test.smol.blog/post/test',
				details: [ 'post_id' => '12345' ],
			)
		]);

		$this->app->execute($command);
	}

	public function testDefaultWillDispatchFailureEvent() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'defaultmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->getId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->defaultHandlerMock->method('push')->willThrowException(new ContentPushException(
			message: 'Authentication expired',
			details: ['code' => 403],
		));

		$this->expectEvents([
			new ContentPushStarted(
				contentId: $content->id,
				channelId: $channel->getId(),
				userId: $content->userId,
				aggregateId: $content->siteId,
			),
			new ContentPushFailed(
				contentId: $content->id,
				channelId: $channel->getId(),
				startEventId: $this->defaultHandlerMock->startEventId, // Use known ID from mock class.
				userId: $content->userId,
				aggregateId: $content->siteId,
				message: 'Authentication expired',
				details: ['code' => 403],
			)
		]);

		$this->app->execute($command);
	}

	public function testItFailsIfTheContentIsMissing() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'defaultmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->getId(),
		);

		$this->contentRepo->method('contentById')->willReturn(null);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->expectNoEvents();
		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheChannelIsMissing() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'defaultmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->getId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn(null);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->expectNoEvents();
		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheUserCannotPush() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'defaultmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->getId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(false);

		$this->expectNoEvents();
		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}
}
