<?php

namespace Smolblog\Mock;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Content\Media\MediaFile;
use Smolblog\Core\Content\Media\MediaHandler as MediaHandlerInterface;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

class MediaHandler implements MediaHandlerInterface {

	public static function getHandle(): string { return 'mock-media'; }

	public function handleUploadedFile(
		UploadedFileInterface $file,
		?Identifier $userId = null,
		?Identifier $siteId = null,
	): MediaFile {
		return new MediaFile(
			id: new RandomIdentifier(),
			handler: self::getHandle(),
			details: [],
		);
	}

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
	): MediaFile {
		return new MediaFile(
			id: new RandomIdentifier(),
			handler: self::getHandle(),
			details: [],
		);
	}

	public function getThumbnailUrlFor(MediaFile $file): string {
		return $file->details['thumbnailUrl'] ?? "//cdn.smol.blog/$file->id/thumb";
	}

	public function getUrlFor(MediaFile $file, ?int $maxWidth = null, ?int $maxHeight = null, mixed ...$props): string {
		return $file->details['thumbnailUrl'] ?? "//cdn.smol.blog/$file->id/full";
	}
}
