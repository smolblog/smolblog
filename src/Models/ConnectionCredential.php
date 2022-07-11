<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

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
	protected array $fields = [
		'id',
		'user_id',
		'provider',
		'provider_key',
		'display_name',
		'details',
	];
}
