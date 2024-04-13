<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Exceptions\InvalidEventPlacement;

class ContentValidationService implements Listener {
	public function __construct(private ContentStateRepo $repo) {
	}

	public function validateCreate(ContentCreated $event): void {
		if ($this->repo->contentExists($event->entityId)) {
			throw new InvalidEventPlacement("Content already exists with ID $event->entityId.");
		}
	}

	public function validateUpdate(ContentUpdated $event): void {
		if ($this->repo->contentExists($event->entityId)) {
			throw new InvalidEventPlacement("No Content exists with ID $event->entityId.");
		}
	}
}
