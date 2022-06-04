<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

/**
 * Stores information about credentials needed to authenticate against an
 * exteral API as a particular user.
 */
class ExternalCredential extends Model {
	/**
	 * Fields available for an ExternalCredential.
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
