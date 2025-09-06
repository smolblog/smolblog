<?php

namespace Smolblog\Foundation\v2\Value\Traits;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Throwable;

use function is_a;

trait CloneKit {
	/**
	 * Create a copy of the object with the given properties replacing existing ones.
	 *
	 * @throws InvalidValueProperties When the object cannot be copied.
	 *
	 * @param mixed ...$props Fields to change for the new object.
	 * @return static
	 */
	public function with(mixed ...$props): static {
		// TODO PHP 8.5 Replace with new clone syntax.
		// Calling get_object_vars from outside context so that we only get public properties.
		// see https://stackoverflow.com/questions/13124072/ for source.
		$base = \get_object_vars(...)->__invoke($this);

		try {
			// @phpstan-ignore-next-line
			$new = new static(...\array_merge($base, $props));
			if (\is_a($new, Validated::class)) {
				$new->validate();
			}

			return $new;
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: 'Unable to copy Value in ' . static::class . '::with(): ' . $e->getMessage(),
				previous: $e,
			);
		}
	}
}
