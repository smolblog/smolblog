<?php

namespace Smolblog\Core\Endpoint;

// This sniff apparently does not support `readonly`.
//phpcs:disable Squiz.Commenting.VariableComment.Missing

/**
 * Represents a request made to an endpoint.
 */
class EndpointRequest {
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
	 * @var array|false
	 */
	public readonly array | false $json;

	/**
	 * Construct the object
	 *
	 * @param integer|null $userId ID of the user making the request if applicable.
	 * @param integer|null $siteId ID of the site request is attached to if applicable.
	 * @param array        $params Parsed params as defined by the Endpoint.
	 * @param array|false  $json   Body of the request as an associative array (false if empty).
	 */
	public function __construct(
		?int $userId = null,
		?int $siteId = null,
		array $params = [],
		array|false $json = false
	) {
		$this->userId = $userId;
		$this->siteId = $siteId;
		$this->params = $params;
		$this->json = $json;
	}
}
