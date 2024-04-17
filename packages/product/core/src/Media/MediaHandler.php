<?php

namespace Smolblog\Core\ContentV1\Media;

use Psr\Http\Message\UploadedFileInterface;
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
	 * @param UploadedFileInterface $file   Uploaded file information.
	 * @param Identifier|null       $userId ID of the user uploading the file.
	 * @param Identifier|null       $siteId Site the file is being uploaded to.
	 * @return MediaFile
	 */
	public function handleUploadedFile(
		UploadedFileInterface $file,
		?Identifier $userId = null,
		?Identifier $siteId = null,
	): MediaFile;

	/**
	 * Sideload the result of the given URL and return the resulting Media object.
	 *
	 * @throws InvalidMediaException If the file at the URL cannot be processed.
	 *
	 * @param string          $url    URL of media to sideload.
	 * @param Identifier|null $userId ID of the user uploading the file.
	 * @param Identifier|null $siteId Site the file is being uploaded to.
	 * @return MediaFile
	 */
	public function sideloadFile(
		string $url,
		?Identifier $userId = null,
		?Identifier $siteId = null,
	): MediaFile;

	/**
	 * Get the URL for a thumbnail image for the media.
	 *
	 * For images, this should be a small version of the image. For videos, a still image. For others, something
	 * representative.
	 *
	 * @param MediaFile $file File to query.
	 * @return string
	 */
	public function getThumbnailUrlFor(MediaFile $file): string;

	/**
	 * Get the URL for this media given the parameters.
	 *
	 * All parameters can be ignored on both sides, but they may be used to provide the optimal file. Any unrecognized
	 * extra props should be ignored. Ideally, the media handler will use this to provide the url to a copy of the media
	 * that will fit in the box provided.
	 *
	 * @param MediaFile    $file      Media object being shown.
	 * @param integer|null $maxWidth  Max width of the media needed.
	 * @param integer|null $maxHeight Max height of the media needed.
	 * @param mixed        ...$props  Any additional props needed.
	 * @return string
	 */
	public function getUrlFor(MediaFile $file, ?int $maxWidth = null, ?int $maxHeight = null, mixed ...$props): string;
}
