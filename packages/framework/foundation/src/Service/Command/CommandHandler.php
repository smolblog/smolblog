<?php

namespace Smolblog\Foundation\Service\Command;

use Attribute;

/**
 * Indicates that the given method executes a Command.
 *
 * There should only ever be one handler for a given Command, and a method shouldn't accept more than one Command.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class CommandHandler {
	/**
	 * Indicates the given method should execute the expected Command.
	 */
	public function __construct() {
	}
}
