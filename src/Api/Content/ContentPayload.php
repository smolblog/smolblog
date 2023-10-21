<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\ArrayType;
use Smolblog\Api\ParameterType;
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
	 * @param Identifier|null       $id         ID of the content.
	 * @param ContentTypePayload    $type       Type information.
	 * @param BaseAttributesPayload $meta       Base attributes.
	 * @param ContentExtension[]    $extensions Extension info.
	 * @param boolean               $published  Set to true to publish the content now.
	 */
	public function __construct(
		public readonly ?Identifier $id = null,
		public readonly ?ContentTypePayload $type = null,
		public readonly ?BaseAttributesPayload $meta = null,
		#[ParameterType(type: 'object')] public readonly array $extensions = [],
		public readonly bool $published = false,
	) {
	}
}
