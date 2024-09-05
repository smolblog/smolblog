<?php

namespace Smolblog\Test\Kits;

use PHPUnit\Framework\Constraint\Constraint;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Framework\Messages\Event;
use Smolblog\Test\Constraints\EventIsEquivalent;

trait EventComparisonTestKit {
	private function eventEquivalentTo(Event|DomainEvent $expected): Constraint {
		return new EventIsEquivalent($expected);
	}
}
