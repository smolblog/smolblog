<?php

namespace Smolblog\Core\Content\Services;

use Smolblog\Core\Content\Commands\{CreateContent, DeleteContent, UpdateContent};
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * A default ContentExtensionService implementation that does nothing.
 *
 * If the extension only needs to store its serialized data, then no events need to be dispatched. The data will be
 * stored with the full content object by the ContentTypeService.
 */
abstract class DefaultContentExtensionService implements ContentExtensionService {
	/**
	 * Create the given content as a new piece of content.
	 *
	 * @param CreateContent $command   Content being created.
	 * @param Identifier    $contentId Definitive ID for the content.
	 * @return void
	 */
	public function create(CreateContent $command, Identifier $contentId): void {
	}

	/**
	 * Update the given content to match this version.
	 *
	 * @param UpdateContent $command Content being updated.
	 * @return void
	 */
	public function update(UpdateContent $command): void {
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
	}
}
