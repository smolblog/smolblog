<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Framework\Objects\Entity;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\NamedIdentifier;

/**
 * Information on a Media's file used by the MediaHandler.
 */
class MediaFile extends Entity {
	/**
	 * Construct the file.
	 *
	 * @param Identifier $id      ID for the file.
	 * @param string     $handler MediaHandler service that will handle this file.
	 * @param array      $details Information needed by the Handler.
	 */
	public function __construct(
		Identifier $id,
		public readonly string $handler,
		public readonly array $details,
	) {
		parent::__construct(id: $id);
	}
}
