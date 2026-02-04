<?php

namespace Smolblog\Core\Content\Types\Picture;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Fields\Markdown;
use Cavatappi\Foundation\Reflection\ListType;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value\ValueKit;
use Crell\Serde\Attributes\Field;
use Smolblog\Core\Content\ContentUtilities;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;

/**
 * An embedded post from another site, such as YouTube or Tumblr.
 */
class Picture implements ContentType, Validated {
	use ValueKit;

	public static function getKey(): string {
		return  'picture';
	}

	/**
	 * Construct the Picture.
	 *
	 * @throws InvalidValueProperties When $pictures contains anything other than Image or Video media.
	 *
	 * @param Media[]       $pictures Pictures being posted.
	 * @param Markdown|null $caption  Optional caption for the Picture.
	 */
	public function __construct(
		#[ListType(Media::class)] public readonly array $pictures,
		public readonly ?Markdown $caption = null,
	) {
		$this->validate();
	}

	/**
	 * Create the title by truncating the caption or .
	 *
	 * @return string
	 */
	#[Field(exclude: true)]
	public string $title {
		get => isset($this->caption)
			? ContentUtilities::truncateText(strval($this->caption))
			: $this->pictures[0]->title;
	}

	public function validate(): void {
		if (empty($this->pictures)) {
			throw new InvalidValueProperties(
				message: 'Pictures cannot be empty.',
				field: 'pictures',
			);
		}

		$rejects = array_filter(
			$this->pictures,
			fn($pic) => !(
				is_a($pic, Media::class)
				&& ($pic->type === MediaType::Image || $pic->type === MediaType::Video)
			),
		);
		if (!empty($rejects)) {
			throw new InvalidValueProperties(
				message: 'Pictures can only contain images or video',
				field: 'pictures',
			);
		}
	}
}
