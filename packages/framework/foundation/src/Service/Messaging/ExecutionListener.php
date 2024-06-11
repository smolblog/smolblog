<?php

namespace Smolblog\Foundation\Service\Messaging;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates the given function/method should fire during the execution phase.
 *
 * This is the default phase (priority 0), and any listener with no priority attribute can be assumed to be
 * equivalent to applying #[ExecutionLayerListener] with no modifications. This attribute is explicitly
 * defined to allow listeners to set priority within the execution phase.
 *
 * @deprecated use CommandHandler, QueryHandler, or EventListener
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class ExecutionListener extends ListenerPriority {
	/**
	 * Indicates the given function/method should fire during the execution phase.
	 *
	 * @param integer $earlier Increases priority by the given integer (will activate sooner).
	 * @param integer $later   Decreases priority by the given integer (will activate later).
	 */
	public function __construct(int $earlier = 0, int $later = 0) {
		parent::__construct(priority: $earlier - $later);
	}
}
