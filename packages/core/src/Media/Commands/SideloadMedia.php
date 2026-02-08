<?php

namespace Smolblog\Core\Media\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\ExpectedResponse;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Reflection\ListType;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Media\Entities\MediaExtension;

/**
 * Fetch the file from the given URL and add it to the media library.
 */
#[ExpectedResponse(type: UuidInterface::class, name: 'id', description: 'ID of the created media')]
readonly class SideloadMedia implements Command, Authenticated, Validated {
	use ValueKit;

	/**
	 * Create the command.
	 *
	 * @throws InvalidValueProperties If title or accessibility text are given and empty.
	 *
	 * @param string             $url               File to sideload.
	 * @param UuidInterface      $userId            User uploading the file.
	 * @param UuidInterface      $siteId            Site file is being uploaded to.
	 * @param string             $accessibilityText Alt text.
	 * @param string|null        $title             Title of the media.
	 * @param UuidInterface|null $mediaId           ID for the new media; will auto-generate if not given.
	 * @param MediaExtension[] $extensions Any extensions added to this media.
	 */
	public function __construct(
		public readonly string $url,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $siteId,
		public readonly string $accessibilityText,
		public readonly ?string $title = null,
		public readonly ?UuidInterface $mediaId = null,
		#[ListType(MediaExtension::class)] public array $extensions = [],
	) {
		$this->validate();
	}

	public function validate(): void {
		if ((isset($this->title) && empty($this->title)) || empty($this->accessibilityText)) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}
	}
}
