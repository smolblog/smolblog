<?php

namespace Smolblog\Core\Media\Entities;

use Smolblog\Core\Media\Events\MediaAttributesUpdated;
use Smolblog\Core\Media\Events\MediaCreated;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\TestCase;

final class MediaEntityTest extends TestCase {
	public function testMediaRequiresANonemptyTitle() {
		$this->expectException(InvalidValueProperties::class);

		new Media(
			id: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: '',
			accessibilityText: 'alt text',
			type: MediaType::Audio,
			handler: 'test',
			fileDetails: [],
		);
	}

	public function testMediaRequiresNonemptyAltText() {
		$this->expectException(InvalidValueProperties::class);

		new Media(
			id: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			title: 'title',
			accessibilityText: '',
			type: MediaType::Audio,
			handler: 'test',
			fileDetails: [],
		);
	}

	public function testMediaCreatedRequiresANonemptyTitle() {
		$this->expectException(InvalidValueProperties::class);

		new MediaCreated(
			entityId: $this->randomId(),
			userId: $this->randomId(),
			aggregateId: $this->randomId(),
			title: '',
			accessibilityText: 'alt text',
			mediaType: MediaType::Audio,
			handler: 'test',
			fileDetails: [],
		);
	}

	public function testMediaCreatedRequiresNonemptyAltText() {
		$this->expectException(InvalidValueProperties::class);

		new MediaCreated(
			entityId: $this->randomId(),
			userId: $this->randomId(),
			aggregateId: $this->randomId(),
			title: 'title',
			accessibilityText: '',
			mediaType: MediaType::Audio,
			handler: 'test',
			fileDetails: [],
		);
	}

	public function testMediaAttributesUpdatedRequiresEitherTitleOrAltText() {
		$this->expectException(InvalidValueProperties::class);

		new MediaAttributesUpdated(
			entityId: $this->randomId(),
			userId: $this->randomId(),
			aggregateId: $this->randomId(),
		);
	}

	public function testMediaAttributesUpdatedRequiresANonemptyTitle() {
		$this->expectException(InvalidValueProperties::class);

		new MediaAttributesUpdated(
			entityId: $this->randomId(),
			userId: $this->randomId(),
			aggregateId: $this->randomId(),
			title: '',
			accessibilityText: 'alt text',
		);
	}

	public function testMediaAttributesUpdatedRequiresNonemptyAltText() {
		$this->expectException(InvalidValueProperties::class);

		new MediaAttributesUpdated(
			entityId: $this->randomId(),
			userId: $this->randomId(),
			aggregateId: $this->randomId(),
			title: 'title',
			accessibilityText: '',
		);
	}
}
