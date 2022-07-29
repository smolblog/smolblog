<?php

namespace Smolblog\Core\Factories;

use Smolblog\Core\ModelHelper;
use Smolblog\Core\Models\ConnectionCredential;

/**
 * Object for making ConnectionCredential models.
 */
class ConnectionCredentialFactory {
	/**
	 * Create the factory
	 *
	 * @param ModelHelper $helper Helper to use when creating Credentials.
	 */
	public function __construct(private ModelHelper $helper) {
	}

	/**
	 * Get a ConnectionCredential model for the given provider and key. This combination is intended
	 * to be unique, such as 'twitter' and an account's Twitter ID.
	 *
	 * @param string $provider Provider slug from the Connector.
	 * @param string $key      Unique identifier for the provider.
	 * @return ConnectionCredential
	 */
	public function credentialWith(string $provider, string $key): ConnectionCredential {
		$cred = new ConnectionCredential(withHelper: $this->helper);
		$cred->loadWithId(['provider' => $provider, 'providerKey' => $key]);
		return $cred;
	}
}
