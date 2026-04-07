<?php

namespace Smolblog\Core\Media\Services;

use Psr\Http\Message\UriInterface;
use Smolblog\Core\Media\Entities\Media;

/**
 * Service that handles media uploads.
 *
 * This could be a service that saves uploads to the filesystem or sends them to an object storage bucket.
 */
interface MediaFileRepo {
	/**
	 * Save the uploaded file and return the resulting file info.
	 *
	 * @throws InvalidMediaException If the file cannot be processed.
	 *
	 * @param resource $file Path to the new file.
	 * @param Media $media System information about the file.
	 * @return array File data
	 */
	public function saveFile($file, Media $mediaObject): array;

	/**
	 * Delete the given Media file from the system.
	 *
	 * @param Media       $media   Media object being deleted.
	 * @return void
	 */
	public function deleteFile(Media $media): void;

	/**
	 * Get the URL for a thumbnail image for the media.
	 *
	 * For images, this should be a small version of the image. For videos, a still image. For others, something
	 * representative.
	 *
	 * @param Media $media Media to query.
	 * @return UriInterface
	 */
	public function getThumbnailUrl(Media $media): UriInterface;

	/**
	 * Get the URL for the canonical version of the media.
	 *
	 * This is the version of the media that any other versions are created from. Other terms might be "full size"
	 * or "original."
	 *
	 * @param Media $media Media to query.
	 * @return UriInterface
	 */
	public function getCanonicalUrl(Media $media): UriInterface;

	/**
	 * Get all available URLs for the media.
	 *
	 * Media is often stored in different sizes for different resolutions and bandwidth needs. This function should
	 * return URLs to all versions of the media keyed by some kind of identifier.
	 *
	 * @param Media        $media     Media object being shown.
	 * @return array<string, UriInterface>
	 */
	public function getUrls(Media $media): array;
}
