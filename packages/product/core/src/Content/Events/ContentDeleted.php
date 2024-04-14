<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Core\Content;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\Fields\DateIdentifier;

readonly class ContentDeleted extends DomainEvent {
	public function __construct(
		Identifier $userId,
		public Content $content,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(
			id: $id ?? new DateIdentifier(),
			timestamp: $timestamp ?? new DateTimeField(),
			userId: $userId,
			aggregateId: $content->siteId,
			entityId: $content->id,
		);
	}

	// run a test; we may not need this?
	// protected static function baseDeserialize(array $data): static {
	// unset($data['aggregateId'], $data['entityId']);
	// return parent::baseDeserialize($data);
	// }
}
