<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\ArrayType;
use Smolblog\Core\Content\ContentExtension;
use Smolblog\Core\Content\ContentType;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * API playload for Content
 */
class ContentPayload extends Value {
	/**
	 * Construct the payload
	 *
	 * @param string                $typeKey    Key of the content type.
	 * @param ContentType           $type       Type information.
	 * @param BaseAttributesPayload $meta       Base attributes.
	 * @param ContentExtension[]    $extensions Extension info.
	 * @param Identifier|null       $id         ID of the content.
	 */
	public function __construct(
		public readonly string $typeKey,
		public readonly ContentType $type,
		public readonly BaseAttributesPayload $meta,
		#[ArrayType(ContentExtension::class)] public readonly array $extensions = [],
		public readonly ?Identifier $id = null,
		public readonly bool $publishNow
	) {
	}
}
