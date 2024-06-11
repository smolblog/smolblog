<?php

namespace Smolblog\Framework\Messages\Attributes;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates the given function/method should fire during the memo save phase.
 *
 * @deprecated Migrate to Smolblog\Foundation classes
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class SaveMemoLayerListener extends ListenerPriority {
	/**
	 * Indicates the given function/method should fire during the memo save phase.
	 *
	 * @param integer $earlier Increases priority by the given integer (will activate sooner).
	 * @param integer $later   Decreases priority by the given integer (will activate later).
	 */
	public function __construct(int $earlier = 0, int $later = 0) {
		parent::__construct(priority: -50 + $earlier - $later);
	}
}
