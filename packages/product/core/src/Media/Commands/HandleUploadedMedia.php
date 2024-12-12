<?php

namespace Smolblog\Core\Media\Commands;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Service\Command\ExpectedResponse;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

/**
 * Save an uploaded file to the media library
 */
#[ExpectedResponse(type: Identifier::class, name: 'id', description: 'ID of the created media')]
readonly class HandleUploadedMedia extends Command {
	/**
	 * Create the command.
	 *
	 * @throws InvalidValueProperties When no accessibility text is provided.
	 *
	 * @param UploadedFileInterface $file              Uploaded file.
	 * @param Identifier            $userId            User uploading the file.
	 * @param Identifier            $siteId            Site file is being uploaded to.
	 * @param string                $accessibilityText Alt text.
	 * @param string|null           $title             Title of the media.
	 * @param Identifier|null       $mediaId           ID for the new media; will auto-generate if not given.
	 */
	public function __construct(
		public UploadedFileInterface $file,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public string $accessibilityText,
		public ?string $title = null,
		public ?Identifier $mediaId = null,
	) {
		if ((isset($title) && empty($title)) || empty($accessibilityText)) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}
		parent::__construct();
	}
}
