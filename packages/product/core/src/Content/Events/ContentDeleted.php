<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Content has been removed and should be purged from the system. Or at least any projections.
 */
readonly class ContentDeleted extends DomainEvent {
}
