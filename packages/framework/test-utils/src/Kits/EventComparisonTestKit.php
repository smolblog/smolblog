<?php

namespace Smolblog\Test\Kits;

use PHPUnit\Framework\Constraint\Constraint;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Constraints\DomainEventChecker;

trait EventComparisonTestKit {
	private function eventEquivalentTo(DomainEvent $expected): Constraint {
		return new DomainEventChecker([$expected]);
	}
}
