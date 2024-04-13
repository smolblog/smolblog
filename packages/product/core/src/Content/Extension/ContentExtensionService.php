<?php

namespace Smolblog\Core\Content\Extension;

use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};

/**
 * Denotes a service for a particular content extension.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 */
interface ContentExtensionService {
	/**
	 * Get the configuration for this content extension.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration;

	/**
	 * Create the given content as a new piece of content.
	 *
	 * @param CreateContent $command Content being created.
	 * @return void
	 */
	public function create(CreateContent $command): void;

	/**
	 * Update the given content to match this version.
	 *
	 * @param UpdateContent $command Content being updated.
	 * @return void
	 */
	public function update(UpdateContent $command): void;

	/**
	 * Delete the given content.
	 *
	 * The full Content object is given here in case it is needed downstream.
	 *
	 * @param DeleteContent $command Content being deleted.
	 * @return void
	 */
	public function delete(DeleteContent $command): void;
}
