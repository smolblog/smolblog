<?php

namespace Smolblog\Core\Media;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Information on a Media's file used by the MediaHandler.
 */
readonly class MediaFile extends Value implements Entity, SerializableValue {
	use EntityKit;
	use SerializableValueKit;

	/**
	 * Construct the file.
	 *
	 * @param Identifier  $id       ID for the file.
	 * @param string      $handler  MediaHandler service that will handle this file.
	 * @param array       $details  Information needed by the Handler.
	 * @param string|null $mimeType Media type (MIME) for the file. Optional.
	 */
	public function __construct(
		Identifier $id,
		public readonly string $handler,
		public readonly array $details,
		public readonly ?string $mimeType = null,
	) {
		$this->id = $id;
	}
}
