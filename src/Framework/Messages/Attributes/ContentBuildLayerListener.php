<?php

namespace Smolblog\Framework\Messages\Attributes;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates the given function/method should fire during the content building phase.
 *
 * Events may only be build with part of the content information, but further listeners may need the entire content
 * state (case in point: the Public* content events). This lets projections and other services set the full Content
 * object on the message so it can be used by the other listeners.
 */
#[Attribute(Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class ContentBuildLayerListener extends ListenerPriority {
	/**
	 * Indicates the given function/method should fire during the content building phase.
	 *
	 * @param integer $earlier Increases priority by the given integer (will activate sooner).
	 * @param integer $later   Decreases priority by the given integer (will activate later).
	 */
	public function __construct(int $earlier = 0, int $later = 0) {
		parent::__construct(priority: 25 + $earlier - $later);
	}
}
