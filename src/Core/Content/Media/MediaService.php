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

		$this->bus->dispatch(new MediaFileAdded(
			contentId: $file->id,
			userId: $command->userId,
			siteId: $command->siteId,
			handler: $file->handler,
			mimeType: $file->mimeType,
			details: $file->details,
		));

		$type = self::typeFromMimeType($file->mimeType ?? $command->file->getClientMediaType() ?? '');

		$this->bus->dispatch(new MediaAdded(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: $command->title ?? $command->file->getClientFilename() ?? $type->name . strval($command->contentId),
			accessibilityText: $command->accessibilityText,
			type: $type,
			thumbnailUrl: $handler->getThumbnailUrlFor(file: $file),
			defaultUrl: $handler->getUrlFor(file: $file),
			defaultHtml: $handler->getHtmlFor(file: $file),
			file: $file,
		));
	}
}
