<?php

namespace Smolblog\App\Endpoint;

use Smolblog\Framework\Value;

/**
 * Represents a request made to an endpoint.
 */
readonly class EndpointRequest extends Value {
	/**
	 * User ID if this is an authenticated request
	 *
	 * @var ?int
	 */
	public readonly ?int $userId;

	/**
	 * Site ID if this is an attached request
	 *
	 * @var ?int
	 */
	public readonly ?int $siteId;

	/**
	 * Parsed params as defined by the Endpoint
	 *
	 * @var array
	 */
	public readonly array $params;

	/**
	 * Body of the request as an associative array; false if the
	 * body is not parsable JSON or is empty.
	 *
	 * @var ?array
	 */
	public readonly ?array $json;

	/**
	 * Construct the object
	 *
	 * @param integer|null $userId ID of the user making the request if applicable.
	 * @param integer|null $siteId ID of the site request is attached to if applicable.
	 * @param array        $params Parsed params as defined by the Endpoint.
	 * @param array|null   $json   Body of the request as an associative array (null if empty).
	 */
	public function __construct(
		?int $userId = null,
		?int $siteId = null,
		array $params = [],
		?array $json = null
	) {
		$this->userId = $userId;
		$this->siteId = $siteId;
		$this->params = $params;
		$this->json = $json;
	}
}
