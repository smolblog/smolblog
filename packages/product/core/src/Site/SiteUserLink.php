<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Objects\Entity;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;

/**
 * Represents a link between a User and a Site.
 *
 * Right now the only permissions being tracked are "can they post" and "can they admin". This may be expanded in the
 * future, but for now this is all.
 */
class SiteUserLink extends Entity {
	public const NAMESPACE = 'fa1f85d0-e650-4ca6-bbef-b2c7b130c689';

	/**
	 * Consistently build a unique identifier based on the site's and user's IDs.
	 *
	 * @param Identifier|string $siteId ID for the site.
	 * @param Identifier|string $userId ID for the user.
	 * @return Identifier
	 */
	public static function buildId(Identifier|string $siteId, Identifier|string $userId): Identifier {
		return new NamedIdentifier(self::NAMESPACE, strval($siteId) . '|' . strval($userId));
	}

	/**
	 * ID of the site linked to this user.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $siteId;

	/**
	 * ID of the user linked to this site.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $userId;

	/**
	 * True if the user is allowed to publish content on this site.
	 *
	 * @var Identifier
	 */
	public readonly bool $isAuthor;

	/**
	 * True if the user is allowed to administer this site.
	 *
	 * @var Identifier
	 */
	public readonly bool $isAdmin;

	/**
	 * Construct the entity
	 *
	 * @param Identifier $siteId   ID of the site linked to this user.
	 * @param Identifier $userId   ID of the user linked to this site.
	 * @param boolean    $isAuthor True if the user is allowed to publish content on this site.
	 * @param boolean    $isAdmin  True if the user is allowed to administer this site.
	 */
	public function __construct(
		Identifier $siteId,
		Identifier $userId,
		bool $isAuthor,
		bool $isAdmin,
	) {
		$this->siteId = $siteId;
		$this->userId = $userId;
		$this->isAuthor = $isAuthor;
		$this->isAdmin = $isAdmin;
		parent::__construct(self::buildId(siteId: $siteId, userId: $userId));
	}
}
