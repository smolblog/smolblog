<?php

namespace Smolblog\Foundation\Value;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Test\TestCase;

#[CoversClass(Keypair::class)]
final class KeypairTest extends TestCase {
	#[TestDox('can be created with a public and private key')]
	public function testBoth() {
		$this->assertInstanceOf(Keypair::class, new Keypair(publicKey: 'public', privateKey: 'private'));
	}

	#[TestDox('can be created with just a public key')]
	public function testJustPublic() {
		$this->assertInstanceOf(Keypair::class, new Keypair(publicKey: 'public'));
	}
}
