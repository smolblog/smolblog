<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

/**
 * Model to represent a user
 */
class User extends Model {
	/**
	 * Fields available for a User.
	 *
	 * @var array
	 */
	protected array $fields = [
		'id',
		'username',
		'email',
	];
}
