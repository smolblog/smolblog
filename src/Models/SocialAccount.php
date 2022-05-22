<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Environment;
use Smolblog\Core\Model;

/**
 * Model for storing info about a social media account
 */
class SocialAccount extends Model {
	/**
	 * Available fields for a SocialAccount instance
	 *
	 * @var array
	 */
	protected array $fields = [
		'id',
		'user_id',
		'social_type',
		'social_username',
		'oauth_token',
		'oauth_secret',
	];
}
