<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content;

/**
 * Denotes a service for a particular content type.
 *
 * Mostly exists to be auto-discovered and added to the registry.
 */
interface ContentTypeService {
	/**
	 * Get the configuration for this content type.
	 *
	 * @return ContentTypeConfiguration
	 */
	public static function getConfiguration(): ContentTypeConfiguration;

	/**
	 * Create the given content as a new piece of content.
	 *
	 * @param Content $content Content being created.
	 * @return void
	 */
	public function create(Content $content): void;

	/**
	 * Update the given content to match this version.
	 *
	 * @param Content $content
	 * @return void
	 */
	public function update(Content $content): void;

	/**
	 * Delete the given content.
	 *
	 * The full Content object is given here in case it is needed downstream.
	 *
	 * @param Content $content
	 * @return void
	 */
	public function delete(Content $content): void;
}
