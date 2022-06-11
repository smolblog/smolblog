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
		'user_id',
		'provider',
		'display_name',
		'details',
	];
}
