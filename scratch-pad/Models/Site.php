<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

/**
 * Model to represent a site
 */
class Site extends Model {
	/**
	 * Fields available for a Site.
	 *
	 * @var array
	 */
	protected array $fields = [
		'id',
		'slug',
		'title',
		'url',
	];
}
