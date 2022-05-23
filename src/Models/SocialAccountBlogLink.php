<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

/**
 * Model for linking a SocialAccount and a Blog
 */
class SocialAccountBlogLink extends Model {
	/**
	 * Available fields for a SocialAccountBlogLink instance
	 *
	 * @var array
	 */
	protected array $fields = [
		'blog_id',
		'social_id',
		'additional_info',
		'can_push',
		'can_pull',
	];
}
