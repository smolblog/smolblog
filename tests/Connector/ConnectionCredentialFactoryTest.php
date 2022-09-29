<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Test\TestModelHelper;

final class ConnectionCredentialFactoryTest extends TestCase {
	public function testACredentialIsCreatedWithTheGivenData() {
		$factory = new ConnectionCredentialFactory(new TestModelHelper());

		$cred = $factory->credentialWith(provider: 'socialNetwork', key: 'account567b');
		$this->assertEquals('socialNetwork', $cred->provider);
		$this->assertEquals('account567b', $cred->providerKey);
	}
}
