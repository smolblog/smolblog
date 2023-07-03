<?php

namespace Smolblog\Core\Content\Media;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Framework\Objects\Identifier;

/**
 * Service that handles media uploads.
 *
 * This could be a service that saves uploads to the filesystem or sends them to an object storage bucket.
 */
interface MediaHandler {
	/**
	 * Get the handle for this handler.
	 *
	 * @return string
	 */
	public static function getHandle(): string;

	/**
	 * Save the uploaded file and return the resulting Media object.
	 *
	 * @param UploadedFileInterface $file   Uploaded file information.
	 * @param Identifier            $userId ID of the user uploading the file.
	 * @param Identifier            $siteId Site the file is being uploaded to.
	 * @return Media
	 */
	public function handleUploadedFile(UploadedFileInterface $file, Identifier $userId, Identifier $siteId): Media;

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

	/**
	 * Get the HTML to display this media given the parameters.
	 *
	 * While `getUrl` provides a raw URL, this provides the full HTML code. If this media has an attribution, this
	 * function should return a `figure` with the required attribution as a caption.
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
	public function getHtmlFor(Media $media, ?int $maxWidth = null, ?int $maxHeight = null, mixed ...$props): string;
}
