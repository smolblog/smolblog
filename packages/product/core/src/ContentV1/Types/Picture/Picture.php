<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\ContentV1\ContentType;
use Smolblog\Core\ContentV1\InvalidContentException;
use Smolblog\Core\ContentV1\Media\MediaType;
use Smolblog\Framework\Objects\SerializableKit;
use Smolblog\Core\ContentV1\Media\Media;
use Smolblog\Core\ContentV1\Types\Note\Note;

/**
 * For showing visual art.
 */
class Picture implements ContentType {
	use SerializableKit {
		toArray as private baseToArray;
	}

	/**
	 * Create the content.
	 *
	 * @throws InvalidContentException When $media is empty or contains something other than images.
	 *
	 * @param Media[]       $media       Media to display.
	 * @param string|null   $caption     Caption for the picture.
	 * @param string[]|null $mediaHtml   Rendered HTML for the media.
	 * @param string|null   $captionHtml Rendered HTML for the caption.
	 */
	public function __construct(
		public readonly array $media,
		public readonly ?string $caption = null,
		private ?array $mediaHtml = [],
		private ?string $captionHtml = '',
	) {
		if (empty($media)) {
			throw new InvalidContentException('A Picture must have at least one media attached.');
		}
		foreach ($media as $item) {
			if ($item->type !== MediaType::Image) {
				throw new InvalidContentException('A Picture can only contain images.');
			}
		}
	}

	/**
	 * Get the title of this content.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return isset($this->caption) ? Note::truncateText($this->caption) : $this->media[0]->title;
	}

	/**
	 * Get the rendered HTML of this content.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		return join("\n\n", $this->mediaHtml) . "\n\n" . $this->captionHtml;
	}

	/**
	 * Get the type key ('picture').
	 *
	 * @return string
	 */
	public function getTypeKey(): string {
		return 'picture';
	}

	/**
	 * Serialize the object.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return [
			...$this->baseToArray(),
			'media' => array_map(fn($media) => $media->toArray(), $this->media),
		];
	}

	/**
	 * Deserialize the object.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		return new Picture(
			media: array_map(fn($arr) => Media::fromArray($arr), $data['media']),
			caption: $data['caption'] ?? null,
			mediaHtml: $data['mediaHtml'] ?? [],
			captionHtml: $data['captionHtml'] ?? '',
		);
	}
}
