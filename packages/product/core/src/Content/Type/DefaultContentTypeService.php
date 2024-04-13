<?php

namespace Smolblog\Core\Content\Type;

use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};
use Smolblog\Core\Content\Events\{ContentCreated, ContentDeleted, ContentUpdated};
use Smolblog\Foundation\Service\Messaging\MessageBus;

abstract class DefaultContentTypeService implements ContentTypeService {
	public function __construct(private MessageBus $bus) {
	}

	protected const CREATE_EVENT = ContentCreated::class;
	protected const UPDATE_EVENT = ContentUpdated::class;
	protected const DELETE_EVENT = ContentDeleted::class;

	/**
	 * Create the given content as a new piece of content.
	 *
	 * @param CreateContent $command Content being created.
	 * @return void
	 */
	public function create(CreateContent $command): void {
		// maybe check for existing ID?

		$this->bus->dispatch(new (static::CREATE_EVENT)(userId: $command->userId, content: $command->content));
	}

	/**
	 * Update the given content to match this version.
	 *
	 * @param UpdateContent $command Content being updated.
	 * @return void
	 */
	public function update(UpdateContent $command): void {
		// TODO: check for existing ID!

		$this->bus->dispatch(new (static::UPDATE_EVENT)(userId: $command->userId, content: $command->content));
	}

	/**
	 * Delete the given content.
	 *
	 * The full Content object is given here in case it is needed downstream.
	 *
	 * @param DeleteContent $command Content being deleted.
	 * @return void
	 */
	public function delete(DeleteContent $command): void {
		// TODO: check for existing ID!

		$this->bus->dispatch(new (static::UPDATE_EVENT)(userId: $command->userId, content: $command->content));
	}
}
