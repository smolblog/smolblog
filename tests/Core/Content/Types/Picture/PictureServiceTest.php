<?php

namespace Smolblog\Core\Content\Types\Picture;

use DateTimeImmutable;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentTypeConfiguration;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Types\Picture\CreatePicture;
use Smolblog\Core\Content\Types\Picture\PictureCreated;
use Smolblog\Core\Content\Types\Picture\PictureService;
use Smolblog\Test\TestCase;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\Kits\ContentTypeServiceTestKit;
use Smolblog\Test\Kits\EventComparisonTestKit;

final class PictureServiceTest extends TestCase {
	use EventComparisonTestKit;
	use ContentTypeServiceTestKit;

	private MessageBus $bus;

	public function setUp(): void {
		$this->bus = $this->createMock(MessageBus::class);
		$this->subject = new PictureService(bus: $this->bus);
	}

	private function setUpDraft(): Content {
		$content = new Content(
			type: $this->createStub(Picture::class),
			id: $this->randomId(),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
		);
		$this->bus->method('fetch')->willReturn($content);
		return $content;
	}

	private function setUpPublished(): Content {
		$content = new Content(
			type: $this->createStub(Picture::class),
			id: $this->randomId(),
			siteId: $this->randomId(),
			authorId: $this->randomId(),
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
		);
		$this->bus->method('fetch')->willReturn($content);
		return $content;
	}

	public function testItHandlesTheCreatePictureCommand() {
		$command = new CreatePicture(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			mediaIds: [$this->randomId()],
			caption: 'Hello, Bumblyburg!',
			contentId: $this->randomId(),
		);

		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo(
			new PictureCreated(
				mediaIds: $command->mediaIds,
				authorId: $command->userId,
				contentId: $command->contentId,
				userId: $command->userId,
				siteId: $command->siteId,
				caption: 'Hello, Bumblyburg!',
			)
		));

		$this->subject->onCreatePicture($command);
	}

	public function testItHandlesTheDeletePictureCommand() {
		$content = $this->setUpDraft();
		$command = new DeletePicture(
			siteId: $content->siteId,
			userId: $content->authorId,
			contentId: $content->id,
		);

		$expectedEvent = new PictureDeleted(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->subject->onDeletePicture($command);
	}

	public function testItSendsAPublicEventIfAPublishedPictureIsDeleted() {
		$content = $this->setUpPublished();
		$command = new DeletePicture(
			siteId: $content->siteId,
			userId: $content->authorId,
			contentId: $content->id,
		);

		$contentArgs = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new PublicPictureRemoved(...$contentArgs))],
			[$this->eventEquivalentTo(new PictureDeleted(...$contentArgs))],
		);

		$this->subject->onDeletePicture($command);
	}

	public function testItHandlesTheEditPictureCaptionCommand() {
		$content = $this->setUpDraft();
		$command = new EditPictureCaption(
			siteId: $content->siteId,
			userId: $content->authorId,
			contentId: $content->id,
			caption: 'Another day',
		);

		$expectedEvent = new PictureCaptionEdited(
			caption: 'Another day',
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->subject->onEditPictureCaption($command);
	}

	public function testItSendsAPublicEventWhenAPublishedPictureHasItsCaptionChanged() {
		$content = $this->setUpPublished();
		$command = new EditPictureCaption(
			siteId: $content->siteId,
			userId: $content->authorId,
			contentId: $content->id,
			caption: 'Another day',
		);
		$contentArgs = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new PictureCaptionEdited(...$contentArgs, caption: 'Another day'))],
			[$this->eventEquivalentTo(new PublicPictureEdited(...$contentArgs))],
		);

		$this->subject->onEditPictureCaption($command);
	}

	public function testItHandlesTheEditPictureMediaCommand() {
		$content = $this->setUpDraft();
		$command = new EditPictureMedia(
			siteId: $content->siteId,
			userId: $content->authorId,
			contentId: $content->id,
			mediaIds: [$this->randomId()],
		);

		$expectedEvent = new PictureMediaEdited(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			mediaIds: $command->mediaIds,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->subject->onEditPictureMedia($command);
	}

	public function testItSendsAPublicEventWhenAPublishedPictureHasItsMediaChanged() {
		$content = $this->setUpPublished();
		$command = new EditPictureMedia(
			siteId: $content->siteId,
			userId: $content->authorId,
			contentId: $content->id,
			mediaIds: [$this->randomId()],
		);
		$contentArgs = [
			'contentId' => $command->contentId,
			'userId' => $command->userId,
			'siteId' => $command->siteId,
		];

		$this->bus->expects($this->exactly(2))->method('dispatch')->withConsecutive(
			[$this->eventEquivalentTo(new PictureMediaEdited(...$contentArgs, mediaIds: $command->mediaIds))],
			[$this->eventEquivalentTo(new PublicPictureEdited(...$contentArgs))],
		);

		$this->subject->onEditPictureMedia($command);
	}

	public function testItHandlesThePublishPictureCommand() {
		$command = new PublishPicture(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $this->randomId(),
		);

		$this->bus->method('fetch')->willReturn(new Content(
			type: $this->createStub(Picture::class),
			siteId: $command->siteId,
			authorId: $command->userId,
		));

		$expectedEvent = new PublicPictureCreated(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
		);
		$this->bus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($expectedEvent));

		$this->subject->onPublishPicture($command);
	}
}
