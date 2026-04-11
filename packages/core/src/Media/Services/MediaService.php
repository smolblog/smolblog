<?php

namespace Smolblog\Core\Media\Services;

use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Utilities\HttpVerb;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Media\Commands\DeleteMedia;
use Smolblog\Core\Media\Commands\EditMediaAttributes;
use Smolblog\Core\Media\Commands\HandleUploadedMedia;
use Smolblog\Core\Media\Commands\SideloadMedia;
use Smolblog\Core\Media\Data\MediaRepo;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Entities\MediaExtension;
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
	 * @param MediaRepo                $mediaRepo Check existing media.
	 * @param MediaFileRepo     $handler  File repository.
	 * @param SitePermissionsService   $perms     Check user permissions.
	 * @param MediaExtensionRegistry $extensions Available MediaExtensions.
	 */
	public function __construct(
		private EventDispatcherInterface $bus,
		private MediaRepo $mediaRepo,
		private MediaFileRepo $handler,
		private SitePermissionsService $perms,
		private MediaExtensionRegistry $extensions,
		private FinfoMimeTypeDetector $mime,
		private ClientInterface $http,
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
		// Check permissions.
		$mediaId = $this->checkNewPermsAndId($command);

		$fileName = $command->file->getClientFilename();
		$filePath = (string)$command->file->getStream()->getMetadata('uri') ?? '';
		$fileStream = $command->file->getStream()->detach();

		if (!isset($fileStream)) {
			throw new InvalidValueProperties('Could not process file.', field: 'file'); // @codeCoverageIgnore
		}

		$media = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: $command->title ?? $fileName ?? $mediaId->toString(),
			accessibilityText: $command->accessibilityText,
			type: self::typeFromMimeType($this->mime->detectMimeType($filePath, $fileStream) ?? 'object/octet-stream'),
			fileDetails: [],
			extensions: $command->extensions,
		);

		$media = $media->with(
			fileDetails: $this->handler->saveFile(file: $fileStream, mediaObject: $media),
		);

		foreach ($this->getServicesForContentExtensions($command->extensions) as $extServ) {
			$extServ->create($media);
		}
		$this->bus->dispatch(MediaCreated::createFromMediaObject($media));

		return $mediaId;
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
		// Check permissions.
		$mediaId = $this->checkNewPermsAndId($command);

		$fileName = basename($command->url->getPath()) ?: $mediaId->toString();

		$response = $this->http->sendRequest(
			HttpMessageFactory::request(HttpVerb::GET, $command->url)
		);

		if ($response->getStatusCode() >= 300) {
			throw new InvalidValueProperties('Could not process download.', field: 'url');
		}

		$filePath = (string)$response->getBody()->getMetadata('uri') ?? '';
		$fileStream = $response->getBody()->detach();

		if (!isset($fileStream)) {
			throw new InvalidValueProperties('Could not process download.', field: 'url'); // @codeCoverageIgnore
		}

		$media = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: $command->title ?? $fileName,
			accessibilityText: $command->accessibilityText,
			type: self::typeFromMimeType($this->mime->detectMimeType($filePath, $fileStream) ?? 'object/octet-stream'),
			fileDetails: [],
			extensions: $command->extensions,
		);

		$media = $media->with(
			fileDetails: $this->handler->saveFile(file: $fileStream, mediaObject: $media),
		);

		foreach ($this->getServicesForContentExtensions($command->extensions) as $extServ) {
			$extServ->create($media);
		}
		$this->bus->dispatch(MediaCreated::createFromMediaObject($media));

		return $mediaId;
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

		foreach ($this->getServicesForContentExtensions($command->extensions ?? []) as $extServ) {
			$extServ->update($command);
		}
		$this->bus->dispatch(new MediaAttributesUpdated(
			entityId: $command->mediaId,
			userId: $command->userId,
			aggregateId: $media->siteId,
			title: $command->title,
			accessibilityText: $command->accessibilityText,
			extensions: $command->extensions,
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

		$this->handler->deleteFile(media: $media);

		foreach ($this->getServicesForContentExtensions($media->extensions) as $extServ) {
			$extServ->delete($command, $media);
		}
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
			} while ($this->mediaRepo->hasMediaWithId($mediaId));
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

	/**
	 * Get extension services for the given MediaExtensions.
	 *
	 * @param MediaExtension[] $extensions Media being worked on.
	 * @return MediaExtensionService[]
	 */
	private function getServicesForContentExtensions(array $extensions): array {
		return array_map(fn($ext) => $this->extensions->serviceForExtensionObject($ext), $extensions);
	}
}
