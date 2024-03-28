<?php
use Smolblog\Framework\Foundation\Value\Keypair;

describe('Keypair::__construct()', function() {
	it('can be created with a public and private key', fn() =>
		expect(new Keypair(publicKey: 'public', privateKey: 'private'))->toBeInstanceOf(Keypair::class)
	);

	it('can be created with just a public key', fn() =>
		expect(new Keypair(publicKey: 'public'))->toBeInstanceOf(Keypair::class)
	);
});
