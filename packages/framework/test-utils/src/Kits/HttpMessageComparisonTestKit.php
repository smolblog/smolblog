<?php

namespace Smolblog\Test\Kits;

use PHPUnit\Framework\Constraint\Constraint;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smolblog\Test\Constraints\HttpMessageIsEquivalent;

trait HttpMessageComparisonTestKit {
	private function httpMessageEqualTo(RequestInterface|ResponseInterface $expected): Constraint {
		return new HttpMessageIsEquivalent($expected);
	}
}
