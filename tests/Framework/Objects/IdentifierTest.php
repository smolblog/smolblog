<?php

namespace Smolblog\Framework\Objects;

use DateInterval;
use Smolblog\Test\TestCase;

final class IdentifierTest extends TestCase {
	public function testARandomIdentifierCanBeCreated() {
		$rand1 = $this->randomId();
		$this->assertInstanceOf(Identifier::class, $rand1);

		$rand2 = $this->randomId();
		$this->assertNotEquals($rand1->toString(), $rand2->toString());
	}

	public function testADateIdentifierCanBeCreated() {
		$yesterday = new DateIdentifier(date_sub(date_create(), new DateInterval('P1D')));
		$today = new DateIdentifier();

		$this->assertLessThan($today->toString(), $yesterday->toString());
	}

	public function testANamespacedIdentifierCanBeCreated() {
		$expected = '3a972fd1-e2bb-56ad-917e-05eaf754b62b';
		$actual = new NamedIdentifier(
			namespace: NamedIdentifier::NAMESPACE_URL,
			name: 'https://smol.blog/test'
		);

		$this->assertEquals($expected, $actual);
	}

	public function testAnIdentifierCanBeMadeFromAString() {
		$expected = '1c1795ad-0581-4ee0-937a-3a7a36e7df74';
		$actual = Identifier::fromString($expected);

		$this->assertEquals($expected, $actual->toString());
	}

	public function testAnIdentifierCanBecomeABinaryString() {
		$createFrom = 'b6520d39-66e5-4ff7-b799-5a9674b17502';
		$actual = Identifier::fromString($createFrom);

		$this->assertEquals(hex2bin('b6520d3966e54ff7b7995a9674b17502'), $actual->toByteString());
	}

	public function testAnIdentifierCanBeMadeFromABinaryString() {
		$createFrom = hex2bin('1d1413ca33d84c2d8029ea41e38654cf');
		$actual = Identifier::fromByteString($createFrom);

		$this->assertEquals('1d1413ca-33d8-4c2d-8029-ea41e38654cf', $actual->toString());
	}

	public function testTheJsonRepresentationIsJustTheString() {
		$expected = 'fb0914b3-0224-4150-bd4b-2934aaddf9be';
		$actual = Identifier::fromString($expected);

		$this->assertEquals("\"$expected\"", json_encode($actual));
	}
}
