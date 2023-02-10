<?php

namespace Smolblog\Core\Content\Types\Reblog;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class ReblogServiceTest extends TestCase {
	use EventComparisonTestKit;

	private MessageBus $bus;
	private ExternalContentService $embed;
	private ReblogService $service;
	private ExternalContentInfo $info;

	public function setUp(): void {
		$this->bus = $this->createMock(MessageBus::class);
		$this->info = new ExternalContentInfo(title: 'No strangers to love', embed: '<iframe src="//youtu.be/video/embed"></iframe>');

		$this->embed = $this->createStub(ExternalContentService::class);
		$this->embed->method('getExternalContentInfo')->willReturn($this->info);

		$this->service = new ReblogService(bus: $this->bus, embedService: $this->embed);
	}

	public function testItHandlesTheCreateReblogCommand() {
		$command = new CreateReblog(
			url: '//smol.blog/',
			userId: Identifier::createRandom(),
			siteId: Identifier::createRandom(),
			publish: false,
			comment: 'Hello.'
		);

		$this->bus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(ReblogCreated::class));

		$this->service->onCreateReblog($command);
	}

	public function testItHandlesTheDeleteReblogCommand() {
		$command = new DeleteReblog(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			reblogId: Identifier::createRandom(),
		);

		$expectedEvent = new ReblogDeleted(
			contentId: $command->reblogId,
			userId: $command->userId,
			siteId: $command->siteId,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->service->onDeleteReblog($command);
	}

	public function testItHandlesTheEditReblogCommentCommand() {
		$command = new EditReblogComment(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			reblogId: Identifier::createRandom(),
			comment: 'Another day',
		);

		$expectedEvent = new ReblogCommentChanged(
			comment: 'Another day',
			contentId: $command->reblogId,
			userId: $command->userId,
			siteId: $command->siteId,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->service->onEditReblogComment($command);
	}

	public function testItHandlesTheEditReblogUrlCommand() {
		$command = new EditReblogUrl(
			siteId: Identifier::createRandom(),
			userId: Identifier::createRandom(),
			reblogId: Identifier::createRandom(),
			url: '//smol.blog/',
		);

		$expectedEvent = new ReblogInfoChanged(
			contentId: $command->reblogId,
			userId: $command->userId,
			siteId: $command->siteId,
			url: '//smol.blog/',
			info: $this->info
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->service->onEditReblogUrl($command);
	}
}
