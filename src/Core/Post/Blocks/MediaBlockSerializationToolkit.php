<?php

namespace Smolblog\Core\Post\Blocks;

use Smolblog\Core\Content\Media as ContentMedia;
use Smolblog\Core\Post\Media;

trait MediaBlockSerializationToolkit {
	/**
	 * Always serialize the media object.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$arr = parent::toArray();
		$arr['media'] = $this->media->toArray();

		return $arr;
	}

	/**
	 * Always unserialize the media field into an object.
	 *
	 * @param array $data Associative array for the block's data.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		$data['media'] = ContentMedia::fromArray($data['media']);

		return parent::fromArray($data);
	}
}
