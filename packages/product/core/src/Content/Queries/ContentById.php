<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\{AuthorizableMessage, Memoizable, MemoizableKit};

readonly class ContentById extends Query implements Memoizable, AuthorizableMessage {
	use MemoizableKit;

	public function __construct(public Identifier $id, public ?Identifier $userId = null)
	{
		parent::__construct();
	}

	public function getAuthorizationQuery(): Query
	{
		return new class () extends Query {
		};
	}
}
