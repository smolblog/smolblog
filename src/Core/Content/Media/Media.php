<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Core\Content\ContentType;

/**
 * Abstract class for handling media uploads.
 */
abstract class Media implements ContentType {
	/**
	 * Construct the media object.
	 *
	 * @param string      $title             Title for the media (filename if nothing else).
	 * @param string      $accessibilityText Alternative text.
	 * @param MediaType   $type              MediaType for this media.
	 * @param string|null $attribution       Optional must-include attribution (will be automatically added to HTML).
	 */
	public function __construct(
		public readonly string $title,
		public readonly string $accessibilityText,
		public readonly MediaType $type,
		public readonly ?string $attribution = null,
	) {
	}

	/**
	 * Get the title of the media.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/**
	 * Get the default HTML for the media.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		return $this->getHtml();
	}

	/**
	 * Get the media handler for this media.
	 *
	 * @return string
	 */
	abstract public function handledBy(): string;

	/**
	 * Get the URL for this media given the parameters.
	 *
	 * All parameters can be ignored on both sides, but they may be used to provide the optimal file. Any unrecognized
	 * extra props should be ignored. Ideally, the media object will use this to provide the url to a copy of the media
	 * that will fit in the box provided.
	 *
	 * @param integer|null $maxWidth  Max width of the media needed.
	 * @param integer|null $maxHeight Max height of the media needed.
	 * @param mixed        ...$props  Any additional props needed.
	 * @return string
	 */
	abstract public function getUrl(?int $maxWidth = null, ?int $maxHeight = null, mixed ...$props): string;

	/**
	 * Get the HTML to display this media given the parameters.
	 *
	 * While `getUrl` provides a raw URL, this provides the full HTML code. If this media has an attribution, this
	 * function should return a `figure` with the required attribution as a caption.
	 *
	 * All parameters can be ignored on both sides, but they may be used to provide the optimal file. Any unrecognized
	 * extra props should be ignored. Ideally, the media object will use this to provide the url to a copy of the media
	 * that will fit in the box provided.
	 *
	 * @param integer|null $maxWidth  Max width of the media needed.
	 * @param integer|null $maxHeight Max height of the media needed.
	 * @param mixed        ...$props  Any additional props needed.
	 * @return string
	 */
	abstract public function getHtml(?int $maxWidth = null, ?int $maxHeight = null, mixed ...$props): string;
}
