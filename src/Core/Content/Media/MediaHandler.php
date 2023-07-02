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
}
