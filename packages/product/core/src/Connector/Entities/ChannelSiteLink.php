<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Objects\Entity;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\NamedIdentifier;

/**
 * Represents a link between a Connection and a Site.
 */
class ChannelSiteLink extends Entity {
	public const NAMESPACE = 'ac8c2a55-53c9-4144-bc08-3c8e2b2f2064';

	/**
	 * Consistently build a unique identifier based on the site's and channel's IDs.
	 *
	 * @param Identifier|string $channelId ID for the user.
	 * @param Identifier|string $siteId    ID for the site.
	 * @return Identifier
	 */
	public static function buildId(Identifier|string $channelId, Identifier|string $siteId): Identifier {
		return new NamedIdentifier(self::NAMESPACE, strval($channelId) . '|' . strval($siteId));
	}

	/**
	 * ID of the channel linked to this site.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $channelId;

	/**
	 * ID of the site linked to this channel.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $siteId;

	/**
	 * Indicates whether this site can push content out to this channel.
	 *
	 * @var Identifier
	 */
	public readonly bool $canPush;

	/**
	 * Indicates whether this site can pull content in from this channel.
	 *
	 * @var Identifier
	 */
	public readonly bool $canPull;

	/**
	 * Construct the entity.
	 *
	 * @param Identifier $channelId ID of the channel linked to this site.
	 * @param Identifier $siteId    ID of the site linked to this channel.
	 * @param boolean    $canPush   Indicates whether this site can push content out to this channel.
	 * @param boolean    $canPull   Indicates whether this site can pull content in from this channel.
	 */
	public function __construct(
		Identifier $channelId,
		Identifier $siteId,
		bool $canPush,
		bool $canPull,
	) {
		$this->channelId = $channelId;
		$this->siteId = $siteId;
		$this->canPush = $canPush;
		$this->canPull = $canPull;

		parent::__construct(self::buildId(channelId: $channelId, siteId: $siteId));
	}
}
