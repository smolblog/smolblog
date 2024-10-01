<?php

namespace Smolblog\Core\Content\Type;

use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};
use Smolblog\Foundation\Service\Registry\ConfiguredRegisterable;

/**
 * Denotes a service for a particular content type.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 */
interface ContentTypeService extends ConfiguredRegisterable {
	/**
	 * Get the configuration for this content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration;

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
