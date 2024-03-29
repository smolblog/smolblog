<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\Api\ArrayType;
use Smolblog\Framework\Objects\Value;

/**
 * Describe a Webfinger object.
 */
class WebfingerResponse extends Value {
	/**
	 * Create the object.
	 *
	 * @param string               $subject    Site this response describes.
	 * @param array|null           $aliases    Other usernames and lookups this site will answer to.
	 * @param array|null           $properties Key-value pairs of metadata.
	 * @param WebfingerLink[]|null $links      URLs belonging to this site.
	 */
	public function __construct(
		public readonly string $subject,
		#[ArrayType('string')] public readonly ?array $aliases = null,
		#[ArrayType('object')] public readonly ?array $properties = null,
		#[ArrayType(WebfingerLink::class)] public readonly ?array $links = null,
	) {
	}

	/**
	 * Serialize this object.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return array_filter(parent::toArray(), fn($item) => isset($item));
	}
}
