<?php

namespace Smolblog\Core\Channel\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Channel\Events\ContentPushFailed;
use Smolblog\Core\Channel\Events\ContentPushStarted;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Channel\Services\ContentPushException;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\Url;
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
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->getId(),
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->handlerMock->expects($this->once())->method('pushContentToChannel')->with(
			content: $content,
			channel: $channel,
			userId: $content->userId,
		);

		$this->app->execute($command);
	}

	public function testHappyPathWithDefaultAsync() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'asyncmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$entry = new ContentChannelEntry(
			contentId: $content->id,
			channelId: $channel->getId(),
			url: new Url('https://test.smol.blog/post/test'),
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
			new ContentPushedToChannel(
				content: $content,
				channelId: $channel->getId(),
				userId: $content->userId,
				entityId: $entry->getId(),
				aggregateId: $content->siteId,
				processId: $this->randomId(),
			),
			new ContentPushSucceeded(
				contentId: $content->id,
				channelId: $channel->getId(),
				processId: $this->randomId(),
				userId: $content->userId,
				entityId: $entry->getId(),
				aggregateId: $content->siteId,
				url: new Url('https://test.smol.blog/post/test'),
				details: [ 'post_id' => '12345' ],
			)
		], checkProcess: true);

		$this->app->execute($command);
	}

	public function testDefaultAsyncWillDispatchFailureEvent() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'asyncmock',
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
			new ContentPushedToChannel(
				content: $content,
				channelId: $channel->getId(),
				userId: $content->userId,
				aggregateId: $content->siteId,
				processId: $this->randomId(),
			),
			new ContentPushFailed(
				contentId: $content->id,
				channelId: $channel->getId(),
				processId: $this->randomId(),
				userId: $content->userId,
				aggregateId: $content->siteId,
				message: 'Authentication expired',
				details: ['code' => 403],
			)
		], checkProcess: true);

		$this->app->execute($command);
	}



	public function testHappyPathWithDefaultProjection() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'projectionmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$entry = new ContentChannelEntry(
			contentId: $content->id,
			channelId: $channel->getId(),
			url: new Url('https://test.smol.blog/post/test'),
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

		$this->defaultProjectionMock->method('project')->willReturn($entry);

		$pushEvent = new ContentPushedToChannel(
			content: $content,
			channelId: $channel->getId(),
			userId: $content->userId,
			entityId: $entry->getId(),
			aggregateId: $content->siteId,
			processId: new DateIdentifier(),
		);
		$this->expectEvents([
			$pushEvent,
			new ContentPushSucceeded(
				contentId: $content->id,
				channelId: $channel->getId(),
				processId: $this->randomId(),
				userId: $content->userId,
				entityId: $entry->getId(),
				aggregateId: $content->siteId,
				url: new Url('https://test.smol.blog/post/test'),
				details: [ 'post_id' => '12345' ],
			)
		], checkProcess: true);

		$this->app->execute($command);
	}

	public function testDefaultProjectionWillDispatchFailureEvent() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'projectionmock',
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

		$this->defaultProjectionMock->method('project')->willThrowException(new ContentPushException(
			message: 'Authentication expired',
			details: ['code' => 403],
		));

		$pushEvent = new ContentPushedToChannel(
			content: $content,
			channelId: $channel->getId(),
			userId: $content->userId,
			aggregateId: $content->siteId,
			processId: $this->randomId(),
		);
		$this->expectEvents([
			$pushEvent,
			new ContentPushFailed(
				contentId: $content->id,
				channelId: $channel->getId(),
				processId: $this->randomId(),
				userId: $content->userId,
				aggregateId: $content->siteId,
				message: 'Authentication expired',
				details: ['code' => 403],
			)
		], checkProcess: true);

		$this->app->execute($command);
	}

	public function testItFailsIfTheContentIsMissing() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'asyncmock',
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
			handler: 'asyncmock',
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
			handler: 'asyncmock',
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
