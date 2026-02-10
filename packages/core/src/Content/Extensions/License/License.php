<?php

namespace Smolblog\Core\Content\Extensions\License;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value\ValueKit;
use Psr\Http\Message\UriInterface;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Content\Extensions\License\LicenseType;
use Smolblog\Core\Media\Entities\MediaExtension;

/**
 * Store a license for a piece of content.
 *
 * This can be original content that is being licensed _or_ tagging a license on existing content. Usable by both
 * Content and Media.
 */
class License implements ContentExtension, MediaExtension, Validated {
	use ValueKit;

	/**
	 * Create the extension
	 *
	 * @throws InvalidValueProperties If any non-string values are given.
	 *
	 * @param string[] $tags Tags as input by the user.
	 */
	public function __construct(
		public readonly bool $originalWork = true,
		public readonly LicenseType $baseType = LicenseType::FullCopyright,
		public readonly ?string $creator = null,
		public readonly ?UriInterface $attributionUrl = null,
		public readonly ?string $requiredAttributionOverride = null,
	) {
		$this->validate();
	}

	public function validate(): void {
		if (isset($this->creator) && empty($this->creator)) {
			throw new InvalidValueProperties('Creator field must not be empty; use null or omit.');
		}
		if (isset($this->requiredAttributionOverride) && empty($this->requiredAttributionOverride)) {
			throw new InvalidValueProperties('requiredAttributionOverride field must not be empty; use null or omit.');
		}
	}
}
