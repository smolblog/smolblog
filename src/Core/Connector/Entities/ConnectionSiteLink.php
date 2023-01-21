<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Objects\Entity;
use Smolblog\Framework\Objects\Identifier;

/**
 * Represents a link between a Connection and a Site.
 */
class ConnectionSiteLink extends Entity {
	public const NAMESPACE = 'ac8c2a55-53c9-4144-bc08-3c8e2b2f2064';

	/**
	 * Consistently build a unique identifier based on the site's and connection's IDs.
	 *
	 * @param Identifier|string $connectionId ID for the user.
	 * @param Identifier|string $siteId       ID for the site.
	 * @return Identifier
	 */
	public static function buildId(Identifier|string $connectionId, Identifier|string $siteId): Identifier {
		return Identifier::createFromName(self::NAMESPACE, strval($connectionId) . '|' . strval($siteId));
	}

	/**
	 * ID of the connection linked to this site.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $connectionId;

	/**
	 * ID of the site linked to this connection.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $siteId;

	/**
	 * Indicates whether this site can push content out to this connection.
	 *
	 * @var Identifier
	 */
	public readonly bool $canPush;

	/**
	 * Indicates whether this site can pull content in from this connection.
	 *
	 * @var Identifier
	 */
	public readonly bool $canPull;

	/**
	 * Construct the entity.
	 *
	 * @param Identifier $connectionId ID of the connection linked to this site.
	 * @param Identifier $siteId       ID of the site linked to this connection.
	 * @param boolean    $canPush      Indicates whether this site can push content out to this connection.
	 * @param boolean    $canPull      Indicates whether this site can pull content in from this connection.
	 */
	public function __construct(
		Identifier $connectionId,
		Identifier $siteId,
		bool $canPush,
		bool $canPull,
	) {
		$this->connectionId = $connectionId;
		$this->siteId = $siteId;
		$this->canPush = $canPush;
		$this->canPull = $canPull;

		parent::__construct(self::buildId(connectionId: $connectionId, siteId: $siteId));
	}
}
