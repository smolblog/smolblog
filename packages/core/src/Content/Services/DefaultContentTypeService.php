<?php

namespace Smolblog\Core\Content\Services;

use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Events\{ContentCreated, ContentDeleted, ContentUpdated};

/**
 * A default ContentTypeService implementation that dispatches the given event for a Command.
 */
abstract class DefaultContentTypeService implements ContentTypeService {
	/**
	 * Construct the service.
	 *
	 * @param EventDispatcherInterface $eventBus For sending the final events.
	 */
	public function __construct(private EventDispatcherInterface $eventBus) {}

	protected const CREATE_EVENT = ContentCreated::class;
	protected const UPDATE_EVENT = ContentUpdated::class;
	protected const DELETE_EVENT = ContentDeleted::class;

	/**
	 * Create the given content as a new piece of content.
	 *
	 * @param CreateContent $command   Content being created.
	 * @param UuidInterface $contentId Definitive ID for the content.
	 * @return void
	 */
	public function create(CreateContent $command, UuidInterface $contentId): void {
		$event = new (static::CREATE_EVENT)(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			contentUserId: $command->contentUserId,
			publishTimestamp: $command->publishTimestamp,
			extensions: $command->extensions,
		);
		$this->eventBus->dispatch($event);
	}

	/**
	 * Update the given content to match this version.
	 *
	 * @param UpdateContent $command Content being updated.
	 * @return void
	 */
	public function update(UpdateContent $command): void {
		$event = new (static::UPDATE_EVENT)(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $command->contentId,
			contentUserId: $command->contentUserId,
			publishTimestamp: $command->publishTimestamp,
			extensions: $command->extensions,
		);
		$this->eventBus->dispatch($event);
	}

	/**
	 * Delete the given content.
	 *
	 * The full Content object is given here in case it is needed downstream.
	 *
	 * @param DeleteContent $command Content being deleted.
	 * @param Content       $content Full content object.
	 * @return void
	 */
	public function delete(DeleteContent $command, Content $content): void {
		$event = new (static::DELETE_EVENT)(
			userId: $command->userId,
			aggregateId: $content->siteId,
			entityId: $content->id,
		);
		$this->eventBus->dispatch($event);
	}
}
