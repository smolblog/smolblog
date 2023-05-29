<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Test\TestCase;

final class KeypairGeneratorTest extends TestCase {
	public function testItReturnsANewKeypairEachTime() {
		$service = new KeypairGenerator();

		$key1 = $service->generate();
		$key2 = $service->generate();

		$this->assertTrue(str_starts_with($key1->publicKey, "-----BEGIN PUBLIC KEY-----"));
		$this->assertTrue(str_starts_with($key1->privateKey, "-----BEGIN PRIVATE KEY-----"));
		$this->assertTrue(str_ends_with($key1->publicKey, "-----END PUBLIC KEY-----"));
		$this->assertTrue(str_ends_with($key1->privateKey, "-----END PRIVATE KEY-----"));
		$this->assertNotEquals($key1, $key2);
	}
}
