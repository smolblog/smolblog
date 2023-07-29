<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\ContentType;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Framework\Objects\SerializableKit;
use Smolblog\Core\Content\Media\Media;

/**
 * For showing visual art.
 */
class Picture implements ContentType {
	use SerializableKit;

	/**
	 * Create the content.
	 *
	 * @throws InvalidContentException When $media is empty.
	 *
	 * @param Media[]       $media       Media to display.
	 * @param string|null   $caption     Caption for the picture.
	 * @param string|null   $givenTitle  Optional title.
	 * @param string[]|null $mediaHtml   Rendered HTML for the media.
	 * @param string|null   $captionHtml Rendered HTML for the caption.
	 */
	public function __construct(
		public readonly array $media,
		public readonly ?string $caption = null,
		private ?string $givenTitle = null,
		private ?array $mediaHtml = null,
		private ?string $captionHtml = null,
	) {
		if (empty($media)) {
			throw new InvalidContentException('A Picture must have at least one media attached.');
		}
	}

	/**
	 * Get the title of this content.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->givenTitle ?? $this->media[0]->title;
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
}
