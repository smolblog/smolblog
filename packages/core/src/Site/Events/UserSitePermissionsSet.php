<?php

namespace Smolblog\Core\Site\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Site\Entities\SitePermissionLevel;

/**
 * Indicates that user permissions have been set for a site.
 */
class UserSitePermissionsSet implements DomainEvent {
	use DomainEventKit;

	/**
	 * Create the event.
	 *
	 * @param UuidInterface          $userId      User making the change.
	 * @param UuidInterface          $aggregateId Site user permissions are being set for.
	 * @param UuidInterface          $entityId    User permissions are being set for.
	 * @param SitePermissionLevel    $level       Level of permissions to set.
	 * @param UuidInterface|null     $id          ID for the event.
	 * @param DateTimeInterface|null $timestamp   Timestamp for the event.
	 * @param UuidInterface|null     $processId   Optional process this event is part of.
	 */
	public function __construct(
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		public readonly UuidInterface $entityId,
		public readonly SitePermissionLevel $level,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}
}
