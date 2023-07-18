<?php

namespace Smolblog\Core\Content\Types\Reblog;

use DateTimeImmutable;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentTypeConfiguration;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\EventComparisonTestKit;

final class ReblogServiceTest extends TestCase {
	use EventComparisonTestKit;

	private Content $reblog;
	private MessageBus $bus;
	private ExternalContentService $embed;
	private ReblogService $service;
	private ExternalContentInfo $info;

	public function setUp(): void {
		$this->info = new ExternalContentInfo(title: 'No strangers to love', embed: '<iframe src="//youtu.be/video/embed"></iframe>');

		$this->embed = $this->createStub(ExternalContentService::class);
		$this->embed->method('getExternalContentInfo')->willReturn($this->info);
	}

	private function setUpDraft() {
		$this->reblog = new Content(
			type: new Reblog(url: '//smol.blog/'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			visibility: ContentVisibility::Draft,
		);

		$this->bus = $this->createMock(MessageBus::class);
		$this->bus->method('fetch')->willReturn($this->reblog);

		$this->service = new ReblogService(bus: $this->bus, embedService: $this->embed);
	}

	private function setUpPublished() {
		$this->reblog = new Content(
			type: new Reblog(url: '//smol.blog/', comment: 'A thing I said.'),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			permalink: '/reblog/a-thing',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
		);

		$this->bus = $this->createMock(MessageBus::class);
		$this->bus->method('fetch')->willReturn($this->reblog);

		$this->service = new ReblogService(bus: $this->bus, embedService: $this->embed);
	}

	public function testItHasAValidConfiguration() {
		$this->assertInstanceOf(ContentTypeConfiguration::class, ReblogService::getConfiguration());
	}

	public function testItHandlesTheCreateReblogCommand() {
		$this->setUpDraft();

		$command = new CreateReblog(
			url: '//smol.blog/',
			userId: $this->randomId(),
			siteId: $this->randomId(),
			publish: false,
			comment: 'Hello.'
		);

		$this->bus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(ReblogCreated::class));

		$this->service->onCreateReblog($command);
	}

	public function testItSendsAPublicEventIfCreateReblogSaysToPublish() {
		$this->setUpDraft();

		$command = new CreateReblog(
			url: '//smol.blog/',
			userId: $this->randomId(),
			siteId: $this->randomId(),
			publish: true,
			comment: 'Hello.'
		);

		$this->bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->isInstanceOf(ReblogCreated::class)],
			[$this->isInstanceOf(PublicReblogCreated::class)],
		);

		$this->service->onCreateReblog($command);
	}

	public function testItHandlesTheDeleteReblogCommand() {
		$this->setUpDraft();

		$command = new DeleteReblog(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
		);

		$expectedEvent = new ReblogDeleted(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->service->onDeleteReblog($command);
	}

	public function testItSendsAPublicEventIfAPublishedReblogIsDeleted() {
		$this->setUpPublished();

		$command = new DeleteReblog(
			siteId: $this->reblog->siteId,
			userId: $this->reblog->authorId,
			contentId: $this->reblog->id,
		);
		$contentArgs = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new PublicReblogRemoved(...$contentArgs))],
			[$this->eventEquivalentTo(new ReblogDeleted(...$contentArgs))],
		);

		$this->service->onDeleteReblog($command);
	}

	public function testItHandlesTheEditReblogCommentCommand() {
		$this->setUpDraft();

		$command = new EditReblogComment(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			comment: 'Another day',
		);

		$expectedEvent = new ReblogCommentChanged(
			comment: 'Another day',
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->service->onEditReblogComment($command);
	}

	public function testItSendsAPublicEventWhenAPublishedReblogHasItsCommentChanged() {
		$this->setUpPublished();

		$command = new EditReblogComment(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			comment: 'Another day',
		);
		$contentArgs = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new ReblogCommentChanged(...$contentArgs, comment: 'Another day'))],
			[$this->eventEquivalentTo(new PublicReblogEdited(...$contentArgs))],
		);

		$this->service->onEditReblogComment($command);
	}

	public function testItHandlesTheEditReblogUrlCommand() {
		$this->setUpDraft();

		$command = new EditReblogUrl(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			url: '//smol.blog/',
		);

		$expectedEvent = new ReblogInfoChanged(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			url: '//smol.blog/',
			info: $this->info
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->service->onEditReblogUrl($command);
	}

	public function testItSendsAPublicEventWhenAPublishedReblogHasItsUrlChanged() {
		$this->setUpPublished();

		$command = new EditReblogUrl(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
			url: '//smol.blog/',
		);
		$contentArgs = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new ReblogInfoChanged(...$contentArgs, url: '//smol.blog/', info: $this->info))],
			[$this->eventEquivalentTo(new PublicReblogEdited(...$contentArgs))],
		);

		$this->service->onEditReblogUrl($command);
	}

	public function testItHandlesThePublishReblogCommand() {
		$this->setUpDraft();

		$command = new PublishReblog(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
		);

		$expectedEvent = new PublicReblogCreated(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->service->onPublishReblog($command);
	}
}
