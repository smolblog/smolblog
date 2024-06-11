<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Core\Content\Events\ContentUpdated;
use Smolblog\Foundation\Exceptions\InvalidEventPlacement;
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Foundation\Service\Messaging\ValidateEventListener;
use Smolblog\Foundation\Service\Messaging\ValidationListener;

/**
 * Check Content events before they are persisted.
 *
 * This is for state validation, such as making sure duplicate content is not being created, or nonexistant content
 * is not being edited.
 */
class ContentValidationService implements EventListenerService {
	/**
	 * Construct the service.
	 *
	 * @param ContentStateRepo $repo Check for existing Content.
	 */
	public function __construct(private ContentStateRepo $repo) {
	}

	/**
	 * Ensure that the Content does not exist.
	 *
	 * @param ContentCreated $event Event to validate.
	 * @return void
	 */
	#[ValidationListener]
	public function validateCreate(ContentCreated $event): void {
		if ($this->repo->contentExists($event->entityId)) {
			throw new InvalidEventPlacement("Content already exists with ID $event->entityId.");
		}
	}

	/**
	 * Ensure that the Content exists.
	 *
	 * @param ContentUpdated $event Event to validate.
	 * @return void
	 */
	#[ValidationListener]
	public function validateUpdate(ContentUpdated $event): void {
		if (!$this->repo->contentExists($event->entityId)) {
			throw new InvalidEventPlacement("No Content exists with ID $event->entityId.");
		}
	}
}
