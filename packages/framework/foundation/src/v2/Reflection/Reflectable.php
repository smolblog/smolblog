<?php

namespace Smolblog\Foundation\v2\Reflection;

use Smolblog\Foundation\Value\ValueProperty;

interface Reflectable {
	/**
	 * Get information about the class' properties.
	 *
	 * @return ValueProperty[]
	 */
	public static function reflect(): array;
}
