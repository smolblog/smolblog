<?php

namespace Smolblog\Foundation\Service\Messaging;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates the given function/method should fire during the event validation phase.
 *
 * This is for methods to check whether a given event is valid given the state of the application.
 *
 * @deprecated use EventListener(earlier: 25)
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class PersistEventListener extends ListenerPriority {
	/**
	 * Indicates the given function/method should fire during the event validation phase.
	 *
	 * @param integer $earlier Increases priority by the given integer (will activate sooner).
	 * @param integer $later   Decreases priority by the given integer (will activate later).
	 */
	public function __construct(int $earlier = 0, int $later = 0) {
		parent::__construct(priority: 25 + $earlier - $later);
	}
}
