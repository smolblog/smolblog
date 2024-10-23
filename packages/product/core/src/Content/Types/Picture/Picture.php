<?php

namespace Smolblog\Core\Content\Types\Picture;

use Smolblog\Core\Content\ContentUtilities;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Content\Fields\Markdown;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;

/**
 * An embedded post from another site, such as YouTube or Tumblr.
 */
readonly class Picture extends ContentType {
	public const KEY = 'picture';

	/**
	 * Construct the Picture.
	 *
	 * @throws InvalidValueProperties When $pictures contains anything other than Image or Video media.
	 *
	 * @param Media[]       $pictures Pictures being posted.
	 * @param Markdown|null $caption  Optional caption for the Picture.
	 */
	public function __construct(
		public array $pictures,
		public ?Markdown $caption = null,
	) {
		if (empty($pictures)) {
			throw new InvalidValueProperties(
				message: 'Pictures cannot be empty.',
				field: 'pictures',
			);
		}

		$rejects = array_filter(
			$pictures,
			fn($pic) => !(
				is_a($pic, Media::class) &&
				($pic->type === MediaType::Image || $pic->type === MediaType::Video)
			)
		);
		if (!empty($rejects)) {
			throw new InvalidValueProperties(
				message: 'Pictures can only contain images or video',
				field: 'pictures',
			);
		}
	}

	/**
	 * Create the title by truncating the caption or .
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return isset($this->caption) ?
			ContentUtilities::truncateText(strval($this->caption)) :
			$this->pictures[0]->title;
	}
}
