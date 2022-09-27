<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;
use Smolblog\Core\Definitions\ModelField;

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
