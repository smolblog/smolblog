<?php
use Smolblog\Framework\Foundation\Service\KeypairGenerator;

describe('KeypairGenerator::generate', function() {
	it('creates a new keypair each time', function() {
		$service = new KeypairGenerator();

		$key1 = $service->generate();
		$key2 = $service->generate();

		expect($key1->publicKey)->toStartWith("-----BEGIN PUBLIC KEY-----");
		expect($key1->privateKey)->toStartWith("-----BEGIN PRIVATE KEY-----");
		expect($key1->publicKey)->toEndWith("-----END PUBLIC KEY-----");
		expect($key1->privateKey)->toEndWith("-----END PRIVATE KEY-----");
		expect($key1)->not->toMatchValue($key2);
	});

	it('normalizes line endings', function() {
		$service = new KeypairGenerator();
		$key = $service->generate();

		expect($key->publicKey)->not->toContain("\r");
		expect($key->publicKey)->toContain("\n");
		expect($key->privateKey)->not->toContain("\r");
		expect($key->privateKey)->toContain("\n");
	});
});
