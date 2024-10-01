<?php

namespace Smolblog\Core\Content\Events;

/**
 * Content has been removed and should be purged from the system. Or at least any projections.
 */
readonly class ContentDeleted extends BaseContentEvent {
}
