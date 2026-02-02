<?php

namespace Smolblog\Core\Media\Services;

use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Factories\UuidFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Media\Commands\DeleteMedia;
use Smolblog\Core\Media\Commands\EditMediaAttributes;
use Smolblog\Core\Media\Commands\HandleUploadedMedia;
use Smolblog\Core\Media\Commands\SideloadMedia;
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaAttributesUpdated;
use Smolblog\Core\Media\Events\MediaCreated;
use Smolblog\Core\Media\Events\MediaDeleted;
use Smolblog\Core\Permissions\SitePermissionsService;

/**
 * Handle tasks related to Media.
 */
class MediaService implements CommandHandlerService {
	/**
	 * Translate a filetype into a MediaType
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Glossary/MIME_type
	 *
	 * @param string $mimeType Given MIME type.
	 * @return MediaType
	 */
	public static function typeFromMimeType(string $mimeType): MediaType {
		return match (strstr($mimeType, '/', true)) { // @codeCoverageIgnore
			false => MediaType::File, // Short-circuit when $mimeType does not have a '/' character.
			'image' => MediaType::Image,
			'video' => MediaType::Video,
			'audio' => MediaType::Audio,
			default => MediaType::File,
		};
	}

	/**
	 * Create the service.
	 *
	 * @param EventDispatcherInterface $bus       MessageBus to dispatch events.
	 * @param MediaHandlerRegistry     $registry  Available MediaHandlers.
	 * @param MediaRepo                $mediaRepo Check existing media.
	 * @param SitePermissionsService   $perms     Check user permissions.
	 */
	public function __construct(
		private EventDispatcherInterface $bus,
		private MediaHandlerRegistry $registry,
		private MediaRepo $mediaRepo,
		private SitePermissionsService $perms,
	) {}

	/**
	 * Handle the HandleUploadMedia command.
	 *
	 * @throws InvalidValueProperties When the given ID is already in use.
	 * @throws CommandNotAuthorized When the user does not have sufficient permissions.
	 *
	 * @param HandleUploadedMedia $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onHandleUploadedMedia(HandleUploadedMedia $command) {
		$mediaId = $this->checkNewPermsAndId($command);

		$handler = $this->registry->get();
		$media = $handler->handleUploadedFile(command: $command, mediaId: $mediaId);

		$this->bus->dispatch(MediaCreated::createFromMediaObject($media));
	}

	/**
	 * Handle the SideloadMedia command.
	 *
	 * @throws InvalidValueProperties When the given ID is already in use.
	 * @throws CommandNotAuthorized When the user does not have sufficient permissions.
	 *
	 * @param SideloadMedia $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onSideloadMedia(SideloadMedia $command) {
		$mediaId = $this->checkNewPermsAndId($command);

		$handler = $this->registry->get();
		$media = $handler->sideloadFile(command: $command, mediaId: $mediaId);

		$this->bus->dispatch(MediaCreated::createFromMediaObject($media));
	}

	/**
	 * Handle the EditMediaAttributes command.
	 *
	 * @throws EntityNotFound When the given Media does not exist.
	 * @throws CommandNotAuthorized When the user does not have sufficient permissions.
	 *
	 * @param EditMediaAttributes $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onEditMediaAttributes(EditMediaAttributes $command) {
		$media = $this->checkEditPermsAndId($command);

		$this->bus->dispatch(new MediaAttributesUpdated(
			entityId: $command->mediaId,
			userId: $command->userId,
			aggregateId: $media->siteId,
			title: $command->title,
			accessibilityText: $command->accessibilityText,
		));
	}

	/**
	 * Handle the DeleteMedia command.
	 *
	 * @throws EntityNotFound When the given Media does not exist.
	 * @throws CommandNotAuthorized When the user does not have sufficient permissions.
	 *
	 * @param DeleteMedia $command Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onDeleteMedia(DeleteMedia $command) {
		$media = $this->checkEditPermsAndId($command);

		$handler = $this->registry->get($media->handler);
		$handler->deleteFile(command: $command, media: $media);

		$this->bus->dispatch(new MediaDeleted(
			entityId: $command->mediaId,
			userId: $command->userId,
			aggregateId: $media->siteId,
		));
	}

	/**
	 * Determine if the given user can edit the given Media.
	 *
	 * @param UuidInterface $userId  User to check.
	 * @param UuidInterface $mediaId Media to check.
	 * @return boolean
	 */
	public function userCanEditMedia(UuidInterface $userId, UuidInterface $mediaId): bool {
		$media = $this->mediaRepo->mediaById($mediaId);
		if (!isset($media)) {
			return false;
		}

		if ($media->userId->equals($userId)) {
			return true;
		}

		return $this->perms->canEditAllMedia(userId: $userId, siteId: $media->siteId);
	}

	/**
	 * Check permissions on the command and return a usable ID.
	 *
	 * @throws InvalidValueProperties When the given ID is already in use.
	 * @throws CommandNotAuthorized When the user does not have sufficient permissions.
	 *
	 * @param HandleUploadedMedia|SideloadMedia $command Command being executed.
	 * @return UuidInterface Valid ID for the new Media object.
	 */
	private function checkNewPermsAndId(HandleUploadedMedia|SideloadMedia $command): UuidInterface {
		// Check for existing ID.
		$mediaId = $command->mediaId;
		if (isset($mediaId) && $this->mediaRepo->hasMediaWithId($mediaId)) {
			throw new InvalidValueProperties(
				message: "The given ID {$mediaId} is already in use.",
				field: 'media.id',
			);
		}

		// Check permissions.
		if (!$this->perms->canUploadMedia(userId: $command->userId, siteId: $command->siteId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		// Generate a new ID.
		if (!isset($mediaId)) {
			do {
				$mediaId = UuidFactory::date();
			} while (!$this->mediaRepo->hasMediaWithId($mediaId));
		}

		return $mediaId;
	}

	/**
	 * Check that the media exists and the user can edit/delete it.
	 *
	 * @throws EntityNotFound When the given Media does not exist.
	 * @throws CommandNotAuthorized When the user does not have sufficient permissions.
	 *
	 * @param EditMediaAttributes|DeleteMedia $command Command to check.
	 * @return Media
	 */
	private function checkEditPermsAndId(EditMediaAttributes|DeleteMedia $command): Media {
		$media = $this->mediaRepo->mediaById($command->mediaId);
		if (!isset($media)) {
			throw new EntityNotFound(entityId: $command->mediaId, entityName: Media::class);
		}

		if (!$this->userCanEditMedia($command->userId, $command->mediaId)) {
			throw new CommandNotAuthorized(originalCommand: $command);
		}

		return $media;
	}
}
