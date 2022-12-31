<?php

namespace Smolblog\Framework\Messages\Attributes;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates the given function/method should fire during the event store phase.
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class EventStoreLayerListener extends ListenerPriority {
	/**
	 * Indicates the given function/method should fire during the event store phase.
	 *
	 * @param integer $earlier Increases priority by the given integer (will activate sooner).
	 * @param integer $later   Decreases priority by the given integer (will activate later).
	 */
	public function __construct(int $earlier = 0, int $later = 0) {
		parent::__construct(priority: 50 + $earlier - $later);
	}
}
