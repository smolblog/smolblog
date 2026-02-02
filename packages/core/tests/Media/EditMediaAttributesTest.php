<?php

namespace Smolblog\Core\Media\Commands;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaAttributesUpdated;
use Smolblog\Core\Test\MediaTestBase;

#[AllowMockObjectsWithoutExpectations]
final class EditMediaAttributesTest extends MediaTestBase {
	public function testHappyPath() {
		$mediaId = $this->randomId();
		$command = new EditMediaAttributes(
			userId: $this->randomId(),
			title: 'Another one',
			accessibilityText: 'A photo of DJ Khaled in front of a green screen.',
			mediaId: $mediaId,
		);
		$media = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $this->randomId(),
			title: 'testimage.jpg',
			accessibilityText: 'Image for testing',
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: [],
		);

		$this->contentRepo->method('mediaById')->willReturn($media);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->expectEvent(new MediaAttributesUpdated(
			entityId: $mediaId,
			aggregateId: $media->siteId,
			userId: $command->userId,
			title: $command->title,
			accessibilityText: $command->accessibilityText,
		));

		$this->app->execute($command);
	}

	public function testItFailsWithoutTitleOrAltText() {
		$this->expectException(InvalidValueProperties::class);

		new EditMediaAttributes(
			mediaId: $this->randomId(),
			userId: $this->randomId(),
		);
	}

	public function testItRequiresANonemptyTitleIfGiven() {
		$this->expectException(InvalidValueProperties::class);

		new EditMediaAttributes(
			mediaId: $this->randomId(),
			userId: $this->randomId(),
			title: '',
		);
	}

	public function testItRequiresANonemptyAltTextIfGiven() {
		$this->expectException(InvalidValueProperties::class);

		new EditMediaAttributes(
			mediaId: $this->randomId(),
			userId: $this->randomId(),
			accessibilityText: '',
		);
	}

	public function testItSucceedsWithTheCorrectPermission() {
		$mediaId = $this->randomId();
		$command = new EditMediaAttributes(
			userId: $this->randomId(),
			title: 'Another one',
			accessibilityText: 'A photo of DJ Khaled in front of a green screen.',
			mediaId: $mediaId,
		);
		$media = new Media(
			id: $mediaId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'testimage.jpg',
			accessibilityText: 'Image for testing',
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: [],
		);

		$this->contentRepo->method('mediaById')->willReturn($media);
		$this->perms->method('canEditAllMedia')->willReturn(true);

		$this->expectEvent(new MediaAttributesUpdated(
			entityId: $mediaId,
			aggregateId: $media->siteId,
			userId: $command->userId,
			title: $command->title,
			accessibilityText: $command->accessibilityText,
		));

		$this->app->execute($command);
	}

	public function testItFailsWithoutPermissionOrMatchingIds() {
		$mediaId = $this->randomId();
		$command = new EditMediaAttributes(
			userId: $this->randomId(),
			title: 'Another one',
			accessibilityText: 'A photo of DJ Khaled in front of a green screen.',
			mediaId: $mediaId,
		);
		$media = new Media(
			id: $mediaId,
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'testimage.jpg',
			accessibilityText: 'Image for testing',
			type: MediaType::Image,
			handler: 'testmock',
			fileDetails: [],
		);

		$this->contentRepo->method('mediaById')->willReturn($media);
		$this->perms->method('canEditAllMedia')->willReturn(false);

		$this->expectNoEvents();
		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheMediaDoesNotExist() {
		$mediaId = $this->randomId();
		$command = new EditMediaAttributes(
			userId: $this->randomId(),
			title: 'Another one',
			accessibilityText: 'A photo of DJ Khaled in front of a green screen.',
			mediaId: $mediaId,
		);

		$this->contentRepo->method('mediaById')->willReturn(null);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->expectNoEvents();
		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}
}
