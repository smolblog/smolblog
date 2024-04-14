<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\ArrayType;
use Smolblog\Api\ParameterType;
use Smolblog\Core\ContentV1\ContentExtension;
use Smolblog\Core\ContentV1\ContentType;
use Smolblog\Core\ContentV1\Extensions\Syndication\Syndication;
use Smolblog\Core\ContentV1\Extensions\Syndication\SyndicationLink;
use Smolblog\Core\ContentV1\Extensions\Tags\Tags;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value;

/**
 * API playload for Content
 */
readonly class ContentPayload extends Value {
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

	/**
	 * Deserialize the payload
	 *
	 * @param array $data Serialized array.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		if (isset($data['extensions']['syndication'])) {
			$data['extensions']['syndication']['channels'] = array_map(
				fn($id) => Identifier::fromString($id),
				$data['extensions']['syndication']['channels'] ?? []
			);
		}
		$published = $data['published'] ?? false;
		unset($data['published']);

		return new ContentPayload(
			id: self::safeDeserializeIdentifier($data['id'] ?? ''),
			type: ContentTypePayload::deserializeValue($data['type']),
			meta: BaseAttributesPayload::deserializeValue($data['meta']),
			extensions: $data['extensions'],
			published: $published,
		);
	}
}
