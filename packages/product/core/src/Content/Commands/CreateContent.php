<?php

namespace Smolblog\Core\Content\Commands;

use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Foundation\Service\Command\ExpectedResponse;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\ArrayType;

/**
 * Create a new piece of Content.
 */
#[ExpectedResponse(type: Identifier::class, name: 'id', description: 'ID of the created content.')]
readonly class CreateContent extends Command {
	/**
	 * Create the command.
	 *
	 * @param Identifier         $userId           ID of the user performing this action.
	 * @param ContentType        $body             ContentType being created.
	 * @param Identifier         $siteId           Site this content is being created for.
	 * @param Identifier|null    $contentId        ID of the new content; will be created if omitted.
	 * @param Identifier|null    $contentUserId    User that is responsible for this content; uses $userId by default.
	 * @param DateTimeField|null $publishTimestamp Time and date content was originally published.
	 * @param ContentExtension[] $extensions       Extension information for this Content.
	 */
	public function __construct(
		public Identifier $userId,
		public ContentType $body,
		public Identifier $siteId,
		public ?Identifier $contentId = null,
		public ?Identifier $contentUserId = null,
		public ?DateTimeField $publishTimestamp = null,
		#[ArrayType(ContentExtension::class)] public array $extensions = [],
	) {
		parent::__construct();
	}
}
