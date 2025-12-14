<?php

namespace Smolblog\Core\Content\Services;

use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentExtensionConfiguration;
use Smolblog\Foundation\Service\Registry\ConfiguredRegisterable;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Denotes a service for a particular content extension.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 */
interface ContentExtensionService extends ConfiguredRegisterable {
	/**
	 * Get the configuration for this content extension.
	 *
	 * @return ContentExtensionConfiguration
	 */
	public static function getConfiguration(): ContentExtensionConfiguration;

	/**
	 * Create the given content as a new piece of content.
	 *
	 * @param CreateContent $command   Content being created.
	 * @param Identifier    $contentId Definitive ID of the content.
	 * @return void
	 */
	public function create(CreateContent $command, Identifier $contentId): void;

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
	 * @param Content       $content Full content object.
	 * @return void
	 */
	public function delete(DeleteContent $command, Content $content): void;
}
