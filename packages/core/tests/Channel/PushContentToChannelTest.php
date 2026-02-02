<?php

namespace Smolblog\Core\Channel\Commands;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Fields\Markdown;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Channel\Events\ContentPushFailed;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Channel\Services\ContentPushException;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Test\ChannelTestBase;

#[AllowMockObjectsWithoutExpectations]
final class PushContentToChannelTest extends ChannelTestBase {
	public function testHappyPathWithCustom() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $this->randomId(),
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
			channelId: $channel->id,
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->handlerMock->expects($this->once())->method('pushContentToChannel')->with(
			content: $this->valueObjectEquals($content),
			channel: $this->valueObjectEquals($channel),
			userId: $this->uuidEquals($content->userId),
		);

		$this->app->execute($command);
	}

	public function testHappyPathWithDefaultAsync() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'asyncmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$entry = new ContentChannelEntry(
			contentId: $content->id,
			channelId: $channel->id,
			url: HttpMessageFactory::uri('https://test.smol.blog/post/test'),
			details: [ 'post_id' => '12345' ],
		);
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->id,
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->defaultHandlerMock->method('push')->willReturn($entry);

		$this->expectEvents([
			new ContentPushedToChannel(
				content: $content,
				channelId: $channel->id,
				userId: $content->userId,
				entityId: $entry->id,
				aggregateId: $content->siteId,
				processId: $this->randomId(),
			),
			new ContentPushSucceeded(
				contentId: $content->id,
				channelId: $channel->id,
				processId: $this->randomId(),
				userId: $content->userId,
				entityId: $entry->id,
				aggregateId: $content->siteId,
				url: HttpMessageFactory::uri('https://test.smol.blog/post/test'),
				details: [ 'post_id' => '12345' ],
			),
		], checkProcess: true);

		$this->app->execute($command);
	}

	public function testDefaultAsyncWillDispatchFailureEvent() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $this->randomId(),
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
			channelId: $channel->id,
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
				channelId: $channel->id,
				userId: $content->userId,
				aggregateId: $content->siteId,
				processId: $this->randomId(),
			),
			new ContentPushFailed(
				contentId: $content->id,
				channelId: $channel->id,
				processId: $this->randomId(),
				userId: $content->userId,
				aggregateId: $content->siteId,
				message: 'Authentication expired',
				details: ['code' => 403],
			),
		], checkProcess: true);

		$this->app->execute($command);
	}



	public function testHappyPathWithDefaultProjection() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $this->randomId(),
		);
		$channel = new BasicChannel(
			handler: 'projectionmock',
			handlerKey: 'test',
			displayName: 'https://test.smol.blog',
			details: [],
		);
		$entry = new ContentChannelEntry(
			contentId: $content->id,
			channelId: $channel->id,
			url: HttpMessageFactory::uri('https://test.smol.blog/post/test'),
			details: [ 'post_id' => '12345' ],
		);
		$command = new PushContentToChannel(
			contentId: $content->id,
			userId: $content->userId,
			channelId: $channel->id,
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->defaultProjectionMock->method('project')->willReturn($entry);

		$pushEvent = new ContentPushedToChannel(
			content: $content,
			channelId: $channel->id,
			userId: $content->userId,
			entityId: $entry->id,
			aggregateId: $content->siteId,
			processId: UuidFactory::date(),
		);
		$this->expectEvents([
			$pushEvent,
			new ContentPushSucceeded(
				contentId: $content->id,
				channelId: $channel->id,
				processId: $this->randomId(),
				userId: $content->userId,
				entityId: $entry->id,
				aggregateId: $content->siteId,
				url: HttpMessageFactory::uri('https://test.smol.blog/post/test'),
				details: [ 'post_id' => '12345' ],
			),
		], checkProcess: true);

		$this->app->execute($command);
	}

	public function testDefaultProjectionWillDispatchFailureEvent() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $this->randomId(),
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
			channelId: $channel->id,
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
			channelId: $channel->id,
			userId: $content->userId,
			aggregateId: $content->siteId,
			processId: $this->randomId(),
		);
		$this->expectEvents([
			$pushEvent,
			new ContentPushFailed(
				contentId: $content->id,
				channelId: $channel->id,
				processId: $this->randomId(),
				userId: $content->userId,
				aggregateId: $content->siteId,
				message: 'Authentication expired',
				details: ['code' => 403],
			),
		], checkProcess: true);

		$this->app->execute($command);
	}

	public function testItFailsIfTheContentIsMissing() {
		$content = new Content(
			body: new Note(text: new Markdown('This is a drill.')),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			id: $this->randomId(),
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
			channelId: $channel->id,
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
			id: $this->randomId(),
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
			channelId: $channel->id,
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
			id: $this->randomId(),
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
			channelId: $channel->id,
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(false);

		$this->expectNoEvents();
		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}
}
