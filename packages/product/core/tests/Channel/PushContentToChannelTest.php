<?php

namespace Smolblog\Core\Channel\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Channel\Entities\BasicChannel;
use Smolblog\Core\Channel\Events\ContentPushStarted;
use Smolblog\Core\Channel\Jobs\ContentPushJob;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Test\ChannelTestBase;

final class PushContentToChannelTest extends ChannelTestBase {
	public function testHappyPathWithCustom() {
		$content = new Content(
			body: $this->createStub(ContentType::class),
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
		$startEvent = new ContentPushStarted(
			contentId: $content->id,
			channelId: $channel->getId(),
			userId: $content->userId,
			aggregateId: $content->siteId,
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->expectEvent($startEvent);

		$this->handlerMock->expects($this->once())->method('pushContentToChannel')->with(
			content: $content,
			channel: $channel,
			userId: $content->userId,
			startEventId: $startEvent->id,
		);

		$this->app->execute($command);
	}

	public function testHappyPathWithDefault() {
		$content = new Content(
			body: $this->createStub(ContentType::class),
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
		$startEvent = new ContentPushStarted(
			contentId: $content->id,
			channelId: $channel->getId(),
			userId: $content->userId,
			aggregateId: $content->siteId,
		);

		$this->contentRepo->method('contentById')->willReturn($content);
		$this->channels->method('channelById')->willReturn($channel);
		$this->perms->method('canPushContent')->willReturn(true);

		$this->expectEvent($startEvent);

		$this->jobs->expects($this->once())->method('enqueue')->with(new ContentPushJob(
			service: \get_class($this->defaultHandlerMock),
			content: $content,
			channel: $channel,
			userId: $content->userId,
			startEventId: $startEvent->id,
		));

		$this->app->execute($command);
	}
}
