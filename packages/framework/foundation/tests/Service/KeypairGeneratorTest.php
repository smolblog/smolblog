<?php

namespace Smolblog\Foundation\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\TestCase;

#[CoversClass(KeypairGenerator::class)]
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

	public function testItNormalizesLineEndings() {
		$service = new KeypairGenerator();
		$key = $service->generate();

		$this->assertStringNotContainsString("\r", $key->publicKey);
		$this->assertStringContainsString("\n", $key->publicKey);
		$this->assertStringNotContainsString("\r", $key->privateKey ?? '');
		$this->assertStringContainsString("\n", $key->privateKey ?? '');
	}
}
