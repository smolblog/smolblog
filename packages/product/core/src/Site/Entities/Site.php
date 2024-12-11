<?php

namespace Smolblog\Core\Site\Entities;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Keypair;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;

/**
 * Represents a site with its own URL, posts, etc.
 */
readonly class Site extends Value implements Entity {
	use EntityKit;

	/**
	 * Construct the site.
	 *
	 * @param Identifier      $id          ID for this site.
	 * @param string          $key         Unique subdomain or subdirectory identifier for this site.
	 * @param string          $displayName Site title as shown in lists and other admin screens.
	 * @param Identifier      $userId      Primary administrator for the site.
	 * @param Keypair         $keypair     Public key tied to the site.
	 * @param string|null     $description Optional description for the site.
	 * @param Identifier|null $pictureId   ID for the site picture.
	 */
	public function __construct(
		Identifier $id,
		public string $key,
		public string $displayName,
		public Identifier $userId,
		public Keypair $keypair,
		public ?string $description = null,
		public ?Identifier $pictureId = null,
	) {
		$this->id = $id;
	}
}
