<?php

namespace Smolblog\Core\Site\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Factories\UuidFactory;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Site\Entities\Site;

/**
 * Indicates a Site has been created.
 */
class SiteCreated implements DomainEvent {
	use DomainEventKit;

	/**
	 * Construct the event.
	 *
	 * @param UuidInterface          $userId      User creating the site.
	 * @param UuidInterface          $aggregateId ID for this site.
	 * @param string                 $key         Unique subdomain or subdirectory identifier for this site.
	 * @param string                 $displayName Site title as shown in lists and other admin screens.
	 * @param UuidInterface|null     $siteUserId  Primary administrator for the site if not $userId.
	 * @param string|null            $description Optional description for the site.
	 * @param UuidInterface|null     $pictureId   ID for the site picture.
	 * @param UuidInterface|null     $id          ID of the event.
	 * @param DateTimeInterface|null $timestamp   Timestamp of the event.
	 * @param UuidInterface|null     $processId   Process that spawned this event.
	 */
	public function __construct(
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		public readonly string $key,
		public readonly string $displayName,
		public readonly UuidInterface $siteUserId,
		public readonly ?string $description = null,
		public readonly ?UuidInterface $pictureId = null,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}

	#[Field(exclude: true)]
	public null $entityId { get => null; }

	/**
	 * Get the Site created by this event.
	 *
	 * @return Site
	 */
	public function getSiteObject(): Site {
		return new Site(
			id: $this->aggregateId ?? UuidFactory::nil(),
			key: $this->key,
			displayName: $this->displayName,
			userId: $this->siteUserId ?? $this->userId,
			description: $this->description,
			pictureId: $this->pictureId,
		);
	}
}
