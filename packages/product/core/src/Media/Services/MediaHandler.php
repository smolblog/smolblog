<?php

namespace Smolblog\Core\Media\Services;

use Smolblog\Core\Media\Commands\HandleUploadedMedia;
use Smolblog\Core\Media\Commands\SideloadMedia;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Foundation\Service\Registry\Registerable;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Service that handles media uploads.
 *
 * This could be a service that saves uploads to the filesystem or sends them to an object storage bucket.
 */
interface MediaHandler extends Registerable {
	/**
	 * Get the handle for this handler.
	 *
	 * @return string
	 */
	public static function getKey(): string;

	/**
	 * Save the uploaded file and return the resulting Media object.
	 *
	 * @throws InvalidMediaException If the file cannot be processed.
	 *
	 * @param HandleUploadedMedia $command Original command being executed.
	 * @param Identifier          $mediaId ID to use to create the Media object.
	 * @return Media
	 */
	public function handleUploadedFile(HandleUploadedMedia $command, Identifier $mediaId): Media;

	/**
	 * Sideload the result of the given URL and return the resulting Media object.
	 *
	 * @throws InvalidMediaException If the file at the URL cannot be processed.
	 *
	 * @param SideloadMedia $command Original command being executed.
	 * @param Identifier    $mediaId ID to use to create the Media object.
	 * @return Media
	 */
	public function sideloadFile(SideloadMedia $command, Identifier $mediaId): Media;

	/**
	 * Get the URL for a thumbnail image for the media.
	 *
	 * For images, this should be a small version of the image. For videos, a still image. For others, something
	 * representative.
	 *
	 * @param Media $media Media to query.
	 * @return string
	 */
	public function getThumbnailUrlFor(Media $media): string;

	/**
	 * Get the URL for this media given the parameters.
	 *
	 * All parameters can be ignored on both sides, but they may be used to provide the optimal file. Any unrecognized
	 * extra props should be ignored. Ideally, the media handler will use this to provide the url to a copy of the media
	 * that will fit in the box provided.
	 *
	 * @param Media        $media     Media object being shown.
	 * @param integer|null $maxWidth  Max width of the media needed.
	 * @param integer|null $maxHeight Max height of the media needed.
	 * @param mixed        ...$props  Any additional props needed.
	 * @return string
	 */
	public function getUrlFor(Media $media, ?int $maxWidth = null, ?int $maxHeight = null, mixed ...$props): string;
}
