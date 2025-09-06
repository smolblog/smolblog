<?php

namespace Smolblog\Foundation\v2\Reflection;

use Smolblog\Foundation\v2\Value;
use Smolblog\Foundation\v2\Value\Traits\CloneKit;

class ValueInfo implements Value {
	use CloneKit;

	public readonly string $type;
	public readonly string $displayName;
	public readonly ?string $description;
	public readonly ?Markdown $longDescription;
}
