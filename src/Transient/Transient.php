<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;
use Smolblog\Core\Definitions\ModelField;

/**
 * An object that needs to persist between pageloads but is not permanent.
 */
class Transient extends Model {
	/**
	 * Fields available for a Transient.
	 *
	 * @var array
	 */
	public const FIELDS = [
		'key' => ModelField::string,
		'value' => ModelField::string,
		'expires' => ModelField::int,
	];
}
