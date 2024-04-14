<?php

namespace Smolblog\Test\Kits;

use PHPUnit\Framework\Constraint\Constraint;
use Smolblog\Foundation\Service\Messaging\Event;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Constraints\EventIsEquivalent;

trait EventComparisonTestKit {
	private function eventEquivalentTo(DomainEvent $expected): Constraint {
		return new EventIsEquivalent($expected);
	}
}
