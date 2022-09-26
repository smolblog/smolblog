<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;
use Smolblog\Core\Definitions\{ModelField, PostStatus};

/**
 * Represents a blog post.
 */
class Post extends Model {
	/**
	 * List of valid fields for the model. Used to verify get/set method. Name of
	 * the field should be the key, value should be a ModelField.
	 *
	 * @var ModelField[]
	 */
	public const FIELDS = [
		'id' => ModelField::int,
		'user_id' => ModelField::int,
		'title' => ModelField::string,
		'content' => ModelField::string,
		'status' => PostStatus::class,
		'slug' => ModelField::string,
		'timestamp' => ModelField::date,
	];
}
