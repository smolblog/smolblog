<?php

namespace Smolblog\Core\Site\Entities;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Represents a site with its own URL, posts, etc.
 */
readonly class Site implements Value, Entity {
	use ValueKit;

	/**
	 * Construct the site.
	 *
	 * @param UuidInterface      $id          ID for this site.
	 * @param string             $key         Unique subdomain or subdirectory identifier for this site.
	 * @param string             $displayName Site title as shown in lists and other admin screens.
	 * @param UuidInterface      $userId      Primary administrator for the site.
	 * @param string|null        $description Optional description for the site.
	 * @param UuidInterface|null $pictureId   ID for the site picture.
	 */
	public function __construct(
		public UuidInterface $id,
		public string $key,
		public string $displayName,
		public UuidInterface $userId,
		public ?string $description = null,
		public ?UuidInterface $pictureId = null,
	) {}
}
