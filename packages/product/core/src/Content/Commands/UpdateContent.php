<?php

namespace Smolblog\Core\Content\Commands;

use Smolblog\Core\Content;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

class UpdateContent extends Command implements AuthorizableMessage {
	public function __construct(
		public Identifier $userId,
		public Content $content,
	) {
		parent::__construct();
	}

	public function getAuthorizationQuery(): UserCanEditContent {
		return new UserCanEditContent(contentId: $this->content->id, userId: $this->userId);
	}
}
