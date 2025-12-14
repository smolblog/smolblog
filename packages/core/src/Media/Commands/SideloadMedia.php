<?php

namespace Smolblog\Core\Media\Commands;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Service\Command\ExpectedResponse;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Fetch the file from the given URL and add it to the media library.
 */
#[ExpectedResponse(type: Identifier::class, name: 'id', description: 'ID of the created media')]
readonly class SideloadMedia extends Command {
	/**
	 * Create the command.
	 *
	 * @throws InvalidValueProperties If title or accessibility text are given and empty.
	 *
	 * @param string          $url               File to sideload.
	 * @param Identifier      $userId            User uploading the file.
	 * @param Identifier      $siteId            Site file is being uploaded to.
	 * @param string          $accessibilityText Alt text.
	 * @param string|null     $title             Title of the media.
	 * @param Identifier|null $mediaId           ID for the new media; will auto-generate if not given.
	 */
	public function __construct(
		public readonly string $url,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly string $accessibilityText,
		public readonly ?string $title = null,
		public readonly ?Identifier $mediaId = null,
	) {
		if ((isset($title) && empty($title)) || empty($accessibilityText)) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}
		parent::__construct();
	}
}
