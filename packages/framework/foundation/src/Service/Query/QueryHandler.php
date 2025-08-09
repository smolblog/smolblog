<?php

namespace Smolblog\Foundation\Service\Query;

use Attribute;
use Crell\Tukio\ListenerPriority;

/**
 * Indicates that the given method executes a Query.
 *
 * There should only ever be one handler for a given Query, and a method shouldn't accept more than one Query.
 *
 * @deprecated Prefer data interfaces
 */
#[Attribute(Attribute::TARGET_METHOD)]
class QueryHandler extends ListenerPriority {
	/**
	 * Indicates the given method should execute the expected Query.
	 */
	public function __construct() {
		parent::__construct(priority: 0);
	}
}
