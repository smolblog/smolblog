<?php

namespace Smolblog\Core\Content\Commands;

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

class UpdateContent extends Command {
	public function __construct(
		public Identifier $userId,
		public Content $content,
	) {
		parent::__construct();
	}
}
