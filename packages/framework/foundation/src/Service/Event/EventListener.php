<?php

namespace Smolblog\Foundation\Service\Event;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates the given function/method should fire during the main phase.
 *
 * This is the default phase (priority 0). At this point the event will have been verified and saved and it is safe
 * to respond to.
 *
 * If the listener should respond to new and replayed events, use ProjectionListener instead.
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class EventListener extends ListenerPriority {
	/**
	 * Indicates the given function/method should fire during the main phase.
	 *
	 * @param integer $earlier Increases priority by the given integer (will activate sooner).
	 * @param integer $later   Decreases priority by the given integer (will activate later).
	 */
	public function __construct(int $earlier = 0, int $later = 0) {
		parent::__construct(priority: $earlier - $later);
	}
}
