<?php

namespace Smolblog\Core\Content\Data;

use Cocur\Slugify\SlugifyInterface;
use Illuminate\Database\ConnectionInterface;
use Smolblog\Foundation\Service\Event\EventListenerService;

class TagsProjection implements EventListenerService {
	public function __construct(
		private ConnectionInterface $db,
		private SlugifyInterface $slugs,
	) {
	}

	// On content create/update/delete, save original and normalized tags to table with content ID.
	// Normalize with $this->slugs->slugify($tag).
}
