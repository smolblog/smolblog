<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;

/**
 * Handle tasks related to Media.
 */
class MediaService implements Listener {
	/**
	 * Translate a filetype into a MediaType
	 *
	 * @see https://developer.mozilla.org/en-US/docs/Glossary/MIME_type
	 *
	 * @param string $mimeType Given MIME type.
	 * @return MediaType
	 */
	public static function typeFromMimeType(string $mimeType): MediaType {
		return match (strstr($mimeType, '/', true)) {
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
	 * @param MessageBus           $bus      MessageBus to dispatch events.
	 * @param MediaHandlerRegistry $registry Available MediaHandlers.
	 */
	public function __construct(
		private MessageBus $bus,
		private MediaHandlerRegistry $registry,
	) {
	}

	/**
	 * Handle the HandleUploadMedia command.
	 *
	 * @param HandleUploadedMedia $command Command to execute.
	 * @return void
	 */
	public function onHandleUploadedMedia(HandleUploadedMedia $command) {
		$handler = $this->registry->get();
		$file = $handler->handleUploadedFile(
			file: $command->file,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$this->createEntities(
			command: $command,
			file: $file,
			thumbnailUrl: $handler->getThumbnailUrlFor(file: $file),
			defaultUrl: $handler->getUrlFor(file: $file),
		);
	}

	/**
	 * Handle the SideloadMedia command.
	 *
	 * @param SideloadMedia $command Command to execute.
	 * @return void
	 */
	public function onSideloadMedia(SideloadMedia $command) {
		$handler = $this->registry->get();
		$file = $handler->sideloadFile(
			url: $command->url,
			userId: $command->userId,
			siteId: $command->siteId,
		);

		$this->createEntities(
			command: $command,
			file: $file,
			thumbnailUrl: $handler->getThumbnailUrlFor(file: $file),
			defaultUrl: $handler->getUrlFor(file: $file),
		);
	}

	/**
	 * Do the actual work of creating entities.
	 *
	 * @param HandleUploadedMedia|SideloadMedia $command      Command being executed.
	 * @param MediaFile                         $file         Handled file.
	 * @param string                            $thumbnailUrl URL to the thumbnail file.
	 * @param string                            $defaultUrl   URL to the actual file.
	 * @return void
	 */
	private function createEntities(
		HandleUploadedMedia|SideloadMedia $command,
		MediaFile $file,
		string $thumbnailUrl,
		string $defaultUrl,
	) {
		$this->bus->dispatch(new MediaFileAdded(
			contentId: $file->id,
			userId: $command->userId,
			siteId: $command->siteId,
			handler: $file->handler,
			mimeType: $file->mimeType,
			details: $file->details,
		));

		$mime = $file->mimeType;
		if (!isset($mime) && property_exists($command, 'file')) {
			$mime = $command->file->getClientMediaType() ?? '';
		}
		$type = self::typeFromMimeType($mime ?? '');

		$title = $type->name . strval($command->contentId);
		if (isset($command->title)) {
			$title = $command->title;
		} elseif (property_exists($command, 'file')) {
			$title = $command->file->getClientFilename();
		}

		$this->bus->dispatch(new MediaAdded(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: $title,
			accessibilityText: $command->accessibilityText,
			type: $type,
			thumbnailUrl: $thumbnailUrl,
			defaultUrl: $defaultUrl,
			file: $file,
		));
	}
}
