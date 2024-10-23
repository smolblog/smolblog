<?php

namespace Smolblog\Foundation\Service\Event;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates the given function/method should fire during the projection phase for both new and replayed events.
 *
 * A benefit to persisting events is that data structures can change drastically, and data can be migrated by re-playing
 * the events. In order for this to work, though, the listeners must be free of side effects. This attribute is for
 * event listeners with no side effects, such as database projections or file generators.
 *
 * For listeners that execute commands or call external services, use EventListener instead.
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class ProjectionListener extends ListenerPriority {
	/**
	 * Indicates the given function/method should fire during the main phase with no side effects.
	 *
	 * @param integer $earlier Increases priority by the given integer (will activate sooner).
	 * @param integer $later   Decreases priority by the given integer (will activate later).
	 */
	public function __construct(int $earlier = 0, int $later = 0) {
		parent::__construct(priority: $earlier - $later);
	}
}
