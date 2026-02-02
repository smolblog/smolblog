<?php

namespace Smolblog\Core\Media\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\ExpectedResponse;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value\ValueKit;
use Crell\Serde\Attributes\ClassNameTypeMap;
use Psr\Http\Message\UploadedFileInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Save an uploaded file to the media library
 */
#[ExpectedResponse(type: UuidInterface::class, name: 'id', description: 'ID of the created media')]
readonly class HandleUploadedMedia implements Command, Authenticated, Validated {
	use ValueKit;

	/**
	 * Create the command.
	 *
	 * @throws InvalidValueProperties When no accessibility text is provided.
	 *
	 * @param UploadedFileInterface $file              Uploaded file.
	 * @param UuidInterface         $userId            User uploading the file.
	 * @param UuidInterface         $siteId            Site file is being uploaded to.
	 * @param string                $accessibilityText Alt text.
	 * @param string|null           $title             Title of the media.
	 * @param UuidInterface|null    $mediaId           ID for the new media; will auto-generate if not given.
	 */
	public function __construct(
		#[ClassNameTypeMap(key: 'implementationType')] public UploadedFileInterface $file,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $siteId,
		public string $accessibilityText,
		public ?string $title = null,
		public ?UuidInterface $mediaId = null,
	) {
		$this->validate();
	}

	public function validate(): void {
		if ((isset($this->title) && empty($this->title)) || empty($this->accessibilityText)) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}
	}
}
