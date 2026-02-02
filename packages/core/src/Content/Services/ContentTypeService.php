<?php

namespace Smolblog\Core\Content\Services;

use Cavatappi\Foundation\Registry\ConfiguredRegisterable;
use Cavatappi\Foundation\Service;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Core\Content\Entities\ContentTypeConfiguration;

/**
 * Denotes a service for a particular content type.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 */
interface ContentTypeService extends ConfiguredRegisterable, Service {
	/**
	 * Get the configuration for this content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration;

	/**
	 * Create the given content as a new piece of content.
	 *
	 * @param CreateContent $command   Content being created.
	 * @param UuidInterface $contentId Definitive ID of the content.
	 * @return void
	 */
	public function create(CreateContent $command, UuidInterface $contentId): void;

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
