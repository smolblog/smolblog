<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Model\{Model, ModelField};

/**
 * Stores information about credentials needed to authenticate against an
 * exteral API as a particular user.
 */
class ConnectionCredential extends Model {
	/**
	 * Fields available for an ConnectionCredential.
	 *
	 * @var array
	 */
	public const FIELDS = [
		'userId' => ModelField::int,
		'provider' => ModelField::string,
		'providerKey' => ModelField::string,
		'displayName' => ModelField::string,
		'details' => ModelField::string,
	];
}
