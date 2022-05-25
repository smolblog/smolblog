<?php

namespace Smolblog\Core;

// This sniff apparently does not support `readonly`.
//phpcs:disable Squiz.Commenting.VariableComment.Missing

use Smolblog\Core\Definitions\HttpVerb;

/**
 * Represents a request made to an endpoint.
 */
class EndpointRequest {
	/**
	 * ID of authenticated user making the request; 0 if anonymous.
	 *
	 * @var integer
	 */
	public readonly int $userId;

	/**
	 * ID of the site this request was made to.
	 *
	 * @var integer
	 */
	public readonly int $siteId;

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
	 * Build the EndpointRequest
	 *
	 * @param integer     $userId ID of authenticated user making the request; 0 if anonymous.
	 * @param integer     $siteId ID of the site this request was made to.
	 * @param array       $params Parsed params as defined by the Endpoint.
	 * @param array|false $json   Body of the request as an associative array (false if empty).
	 */
	public function __construct(
		int $userId = 0,
		int $siteId = 0,
		array $params = [],
		array|false $json = false
	) {
		$this->userId = $userId;
		$this->siteId = $siteId;
		$this->params = $params;
		$this->json = $json;
	}
}
