<?php

namespace Smolblog\Core\Content\Commands;

use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Content\Entities\ContentType;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\ArrayType;

/**
 * Update the given content with new information. Will replace all fields with the given values, including omitted
 * fields.
 */
readonly class UpdateContent extends Command {
	/**
	 * Create the command.
	 *
	 * @param Identifier         $contentId        ID of the new content; will be created if omitted.
	 * @param Identifier         $userId           ID of the user performing this action.
	 * @param ContentType        $body             ContentType being created.
	 * @param Identifier         $siteId           Site this content is being created for.
	 * @param Identifier         $contentUserId    User that is responsible for this content.
	 * @param DateTimeField|null $publishTimestamp Time and date content was originally published.
	 * @param ContentExtension[] $extensions       Extension information for this Content.
	 */
	public function __construct(
		public Identifier $contentId,
		public Identifier $userId,
		public ContentType $body,
		public Identifier $siteId,
		public Identifier $contentUserId,
		public ?DateTimeField $publishTimestamp = null,
		#[ArrayType(ContentExtension::class)] public array $extensions = [],
	) {
		parent::__construct();
	}
}
