<?php

namespace Smolblog\Framework\MessageBus\Attributes;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates the given function/method should fire during the memo save phase.
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class SaveMemoLayerListener extends ListenerPriority {
	/**
	 * Indicates the given function/method should fire during the memo save phase.
	 *
	 * @param integer $before Increases priority by the given integer (will activate sooner).
	 * @param integer $after  Decreases priority by the given integer (will activate later).
	 */
	public function __construct(int $before = 0, int $after = 0) {
		parent::__construct(priority: -50 + $before - $after);
	}
}
