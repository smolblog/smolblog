<?php

namespace Smolblog\MicroBlog;

use Smolblog\Framework\Objects\DomainModel;

class Model extends DomainModel {
	public const SERVICES = [
		MicroBlogConnector::class => [],
	];
}
