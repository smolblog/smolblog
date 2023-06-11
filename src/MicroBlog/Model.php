<?php

namespace Smolblog\MicroBlog;

use Smolblog\Framework\Objects\DomainModel;

/**
 * Services for the Micro.blog connector
 */
class Model extends DomainModel {
	public const SERVICES = [
		MicroBlogConnector::class => [],
	];
}
