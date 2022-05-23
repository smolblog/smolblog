<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Model;

/**
 * Model for linking a SocialAccount and a Blog
 */
class SocialAccountSiteLink extends Model {
	/**
	 * Available fields for a SocialAccountsiteLink instance. The 'id' in this
	 * case is the combination of 'site_id', 'socialaccount_id', and 'additional_info'.
	 *
	 * @var array
	 */
	protected array $fields = [
		'site_id',
		'socialaccount_id',
		'additional_info',
		'can_push',
		'can_pull',
	];
}
