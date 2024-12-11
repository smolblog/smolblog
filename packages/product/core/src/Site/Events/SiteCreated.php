<?php

namespace Smolblog\Core\Site\Events;

use Smolblog\Core\Site\Entities\Site;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Keypair;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates a Site has been created.
 */
readonly class SiteCreated extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * @param Identifier         $userId      User creating the site.
	 * @param Identifier         $aggregateId ID for this site.
	 * @param string             $key         Unique subdomain or subdirectory identifier for this site.
	 * @param string             $displayName Site title as shown in lists and other admin screens.
	 * @param Keypair            $keypair     Key tied to the site.
	 * @param Identifier|null    $siteUserId  Primary administrator for the site if not $userId.
	 * @param string|null        $description Optional description for the site.
	 * @param Identifier|null    $pictureId   ID for the site picture.
	 * @param Identifier|null    $id          ID of the event.
	 * @param DateTimeField|null $timestamp   Timestamp of the event.
	 * @param Identifier|null    $processId   Process that spawned this event.
	 */
	public function __construct(
		Identifier $userId,
		Identifier $aggregateId,
		public string $key,
		public string $displayName,
		public Keypair $keypair,
		public Identifier $siteUserId,
		public ?string $description = null,
		public ?Identifier $pictureId = null,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $processId = null,
	) {
		parent::__construct(
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			processId: $processId,
		);
	}

	/**
	 * Remove 'entityId' from (de)serialization.
	 *
	 * @return array
	 */
	protected static function propertyInfo(): array {
		$base = parent::propertyInfo();
		unset($base['entityId']);
		return $base;
	}

	/**
	 * Get the Site created by this event.
	 *
	 * @return Site
	 */
	public function getSiteObject(): Site {
		return new Site(
			id: $this->aggregateId ?? Identifier::nil(),
			key: $this->key,
			displayName: $this->displayName,
			userId: $this->siteUserId ?? $this->userId,
			keypair: $this->keypair,
			description: $this->description,
			pictureId: $this->pictureId,
		);
	}
}
