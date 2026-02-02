<?php

namespace Smolblog\Core\Media\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Change the attributes on a media object.
 */
readonly class EditMediaAttributes implements Command, Authenticated, Validated {
	use ValueKit;

	/**
	 * Construct the command.
	 *
	 * @throws InvalidValueProperties Thrown if no updated attributes are given.
	 *
	 * @param UuidInterface $mediaId           ID of the media being edited.
	 * @param UuidInterface $userId            User making this change.
	 * @param string|null   $title             New title.
	 * @param string|null   $accessibilityText New alt text.
	 */
	public function __construct(
		public readonly UuidInterface $mediaId,
		public readonly UuidInterface $userId,
		public readonly ?string $title = null,
		public readonly ?string $accessibilityText = null,
	) {
		$this->validate();
	}

	public function validate(): void {
		if (!isset($this->title) && !isset($this->accessibilityText)) {
			throw new InvalidValueProperties('No updated attributes provided.');
		}
		if (
			(isset($this->title) && empty($this->title))
			|| (isset($this->accessibilityText) && empty($this->accessibilityText))
		) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}
	}
}
