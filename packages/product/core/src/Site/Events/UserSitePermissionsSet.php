<?php

namespace Smolblog\Core\Site\Events;

use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates that user permissions have been set for a site.
 */
readonly class UserSitePermissionsSet extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Identifier          $userId      User making the change.
	 * @param Identifier          $aggregateId Site user permissions are being set for.
	 * @param Identifier          $entityId    User permissions are being set for.
	 * @param SitePermissionLevel $level       Level of permissions to set.
	 * @param Identifier|null     $id          ID for the event.
	 * @param DateTimeField|null  $timestamp   Timestamp for the event.
	 * @param Identifier|null     $processId   Optional process this event is part of.
	 */
	public function __construct(
		Identifier $userId,
		Identifier $aggregateId,
		Identifier $entityId,
		public SitePermissionLevel $level,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $processId = null,
	) {
		parent::__construct(
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			entityId: $entityId,
			processId: $processId,
		);
	}
}
