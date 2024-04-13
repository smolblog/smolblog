<?php

namespace Smolblog\Core\Content\Commands;

use Smolblog\Core\Content;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

readonly class UpdateContent extends Command implements AuthorizableMessage {
	public function __construct(
		public Identifier $userId,
		public Content $content,
	) {}

	public function getAuthorizationQuery(): Query {
		return new class() extends Query {};
	}
}
