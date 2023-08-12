<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\ContentType;
use Smolblog\Core\Content\InvalidContentException;
use Smolblog\Core\Content\Media\MediaType;
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
	 * @throws InvalidContentException When $media is empty or contains something other than images.
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
		return $this->givenTitle ?? $this->media[0]->title;
	}

	/**
	 * Get the rendered HTML of this content.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		$mediaHtmlBlocks = $this->mediaHtml ?? self::createBasicHtmlForImageMedia($this->media);

		return join("\n\n", $mediaHtmlBlocks) . "\n\n" . $this->captionHtml;
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
	 * Create basic HTML for a Picture's media array.
	 *
	 * @param array $media Array of images.
	 * @return array
	 */
	public static function createBasicHtmlForImageMedia(array $media): array {
		return array_map(fn($m) => "<img src='$m->defaultUrl' alt='$m->accessabilityText'>", $media);
	}
}
