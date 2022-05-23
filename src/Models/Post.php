<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

/**
 * Model to represent a single post
 */
class Post extends Model {
	/**
	 * Fields available for a Post.
	 *
	 * @var array
	 */
	protected array $fields = [
		'id',
		'slug',
		'title',
		'import_key',
		'content',
		'date',
		'status',
		'user_id',
		'media',
		'tags',
		'reblog',
		'meta',
	];
}
