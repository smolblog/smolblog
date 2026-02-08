<?php

namespace Smolblog\Core\Media\Services;

use Cavatappi\Foundation\Registry\ConfiguredRegisterable;
use Cavatappi\Foundation\Service;
use Smolblog\Core\Media\Commands\{DeleteMedia, EditMediaAttributes};
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaExtensionConfiguration;

/**
 * Denotes a service for a particular media extension.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 */
interface MediaExtensionService extends ConfiguredRegisterable, Service {
	/**
	 * Get the configuration for this media extension.
	 *
	 * @return MediaExtensionConfiguration
	 */
	public static function getConfiguration(): MediaExtensionConfiguration;

	/**
	 * Create the given media as a new piece of media.
	 *
	 * This is called before the MediaCreated event is fired
	 *
	 * @param Media $media Created media object
	 * @return void
	 */
	public function create(Media $media): void;

	/**
	 * Update the given media attributes to match this version.
	 *
	 * @param EditMediaAttributes $command Media being updated.
	 * @return void
	 */
	public function update(EditMediaAttributes $command): void;

	/**
	 * Delete the given media.
	 *
	 * The full Media object is given here in case it is needed downstream.
	 *
	 * @param DeleteMedia $command Media being deleted.
	 * @param Media       $media Full media object.
	 * @return void
	 */
	public function delete(DeleteMedia $command, Media $media): void;
}
