<?php

namespace Smolblog\Infrastructure\OpenApi;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Email;
use Smolblog\Foundation\Value\Fields\Markdown;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * General info for an OpenAPI Spec
 */
readonly class OpenApiSpecInfo extends Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Construct the object.
	 *
	 * @param string        $title          Title of the API.
	 * @param string        $version        Version of the API.
	 * @param Url           $serverUrl      URL of the server.
	 * @param Markdown|null $description    Longform description for the API.
	 * @param string|null   $contactName    Optional contact name.
	 * @param Url|null      $contactUrl     Optional contact URL.
	 * @param Email|null    $contactEmail   Optional contact email address.
	 * @param string        $authSchemeName Name of the auth scheme used; default 'default'.
	 * @param array         $overrides      The generated spec will be merged with this.
	 */
	public function __construct(
		public string $title,
		public string $version,
		public Url $serverUrl,
		public ?Markdown $description = null,
		public ?string $contactName = null,
		public ?Url $contactUrl = null,
		public ?Email $contactEmail = null,
		public string $authSchemeName = 'default',
		public array $overrides = [],
	) {
	}
}
