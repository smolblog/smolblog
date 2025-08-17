<?php

namespace Smolblog\Foundation\v2\Value;

use Smolblog\Foundation\Value\Fields\Identifier;

interface Entity {
	public Identifier $id { get; }
}
