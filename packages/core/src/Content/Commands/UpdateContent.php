<?php

namespace Smolblog\Core\Content\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Reflection\ListType;
use Cavatappi\Foundation\Value\ValueKit;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Content\Entities\ContentType;

/**
 * Update the given content with new information. Will replace all fields with the given values, including omitted
 * fields.
 */
readonly class UpdateContent implements Command, Authenticated {
	use ValueKit;

	/**
	 * Create the command.
	 *
	 * @param UuidInterface          $contentId        ID of the new content; will be created if omitted.
	 * @param UuidInterface          $userId           ID of the user performing this action.
	 * @param ContentType            $body             ContentType being created.
	 * @param UuidInterface          $siteId           Site this content is being created for.
	 * @param UuidInterface          $contentUserId    User that is responsible for this content.
	 * @param DateTimeInterface|null $publishTimestamp Time and date content was originally published.
	 * @param ContentExtension[]     $extensions       Extension information for this Content.
	 */
	public function __construct(
		public UuidInterface $contentId,
		public UuidInterface $userId,
		public ContentType $body,
		public UuidInterface $siteId,
		public UuidInterface $contentUserId,
		public ?DateTimeInterface $publishTimestamp = null,
		#[ListType(ContentExtension::class)] public array $extensions = [],
	) {}
}
