<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\ContentType;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Core\Content\Media\MediaType;
use Smolblog\Framework\Objects\SerializableKit;
use Smolblog\Core\Content\Media\Media;
use Smolblog\Core\Content\Types\Note\Note;

/**
 * For showing visual art.
 */
class Picture implements ContentType {
	use SerializableKit;

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
		private ?array $mediaHtml = null,
		private ?string $captionHtml = null,
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
}
