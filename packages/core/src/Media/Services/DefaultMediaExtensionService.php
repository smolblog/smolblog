<?php

namespace Smolblog\Core\Media\Services;

use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Media\Commands\DeleteMedia;
use Smolblog\Core\Media\Commands\EditMediaAttributes;
use Smolblog\Core\Media\Entities\Media;

/**
 * A default MediaExtensionService implementation that does nothing.
 *
 * If the extension only needs to store its serialized data, then no events need to be dispatched. The data will be
 * stored with the full content object by the MediaTypeService.
 */
abstract class DefaultMediaExtensionService implements MediaExtensionService {
	/**
	 * Create the given media as a new piece of media.
	 *
	 * This is called before the MediaCreated event is fired
	 *
	 * @param Media $media Created media object
	 * @return void
	 */
	public function create(Media $media): void {}

	/**
	 * Update the given media attributes to match this version.
	 *
	 * @param EditMediaAttributes $command Media being updated.
	 * @return void
	 */
	public function update(EditMediaAttributes $command): void {}

	/**
	 * Delete the given media.
	 *
	 * The full Media object is given here in case it is needed downstream.
	 *
	 * @param DeleteMedia $command Media being deleted.
	 * @param Media       $media Full media object.
	 * @return void
	 */
	public function delete(DeleteMedia $command, Media $media): void {}
}
