<?php

namespace Smolblog\Core\Site\Entities;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Define the permissions for a given user on a given site.
 */
readonly class UserSitePermissions extends Value implements Entity, SerializableValue {
	use SerializableValueKit;

	public const NAMESPACE = 'd1878826-9a9c-4977-9569-4cb683b60947';

	/**
	 * Consistently build a unique identifier from the User and Site IDs.
	 *
	 * @param string|Identifier $userId ID of the user.
	 * @param string|Identifier $siteId ID of the site.
	 * @return Identifier
	 */
	public static function buildId(string|Identifier $userId, string|Identifier $siteId): Identifier {
		return new NamedIdentifier(namespace: self::NAMESPACE, name: "$userId|$siteId");
	}

	/**
	 * True if user is an administrator for this site.
	 *
	 * @var boolean
	 */
	public bool $isAdmin;

	/**
	 * True if user can edit all content on this site, including content not authored by them.
	 *
	 * @var boolean
	 */
	public bool $canEditAllContent;

	/**
	 * True if user can create content for this site.
	 *
	 * @var boolean
	 */
	public bool $canCreateContent;

	/**
	 * Create the object.
	 *
	 * Some permissions imply others; an exception will be thrown if illogical permissons are given. Particularly, if
	 * `isAdmin` is true, all permissions should be true as well. If you're creating the object yourself, use named
	 * arguments and only define the permissions you know should be set.
	 *
	 * @throws InvalidValueProperties When inconsistent or invalid permission combinations are given.
	 *
	 * @param Identifier   $userId            User these permissions are for.
	 * @param Identifier   $siteId            Site these permissions are for.
	 * @param boolean|null $isAdmin           True if user is an administrator for this site.
	 * @param boolean|null $canEditAllContent True if user can edit all content on this site.
	 * @param boolean|null $canCreateContent  True if user can create content for this site.
	 */
	public function __construct(
		public Identifier $userId,
		public Identifier $siteId,
		?bool $isAdmin = null,
		?bool $canEditAllContent = null,
		?bool $canCreateContent = null,
	) {
		if ($isAdmin && ($canCreateContent === false || $canEditAllContent === false)) {
			throw new InvalidValueProperties('Invalid permission combination');
		}

		$this->isAdmin = $isAdmin ?? false;
		$this->canEditAllContent = $canEditAllContent ?? $this->isAdmin;
		$this->canCreateContent = $canCreateContent ?? $this->canEditAllContent;
	}

	/**
	 * Get the ID based on user and site IDs.
	 *
	 * @return Identifier
	 */
	public Identifier $id {
		get => self::buildId($this->userId, $this->siteId);
	}
}
