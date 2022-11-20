<?php

namespace Smolblog\Framework;

use DateInterval;
use PHPUnit\Framework\TestCase;

final class IdentifierTest extends TestCase {
	public function testARandomIdentifierCanBeCreated() {
		$rand1 = Identifier::createRandom();
		$this->assertInstanceOf(Identifier::class, $rand1);

		$rand2 = Identifier::createRandom();
		$this->assertNotEquals($rand1->toString(), $rand2->toString());
	}

	public function testADateIdentifierCanBeCreated() {
		$yesterday = Identifier::createFromDate(date_sub(date_create(), new DateInterval('P1D')));
		$today = Identifier::createFromDate();

		$this->assertLessThan($today->toString(), $yesterday->toString());
	}

	public function testANamespacedIdentifierCanBeCreated() {
		$expected = '3a972fd1-e2bb-56ad-917e-05eaf754b62b';
		$actual = Identifier::createFromName(
			namespace: Identifier::NAMESPACE_URL,
			name: 'https://smol.blog/test'
		);

		$this->assertEquals($expected, $actual);
	}
}
