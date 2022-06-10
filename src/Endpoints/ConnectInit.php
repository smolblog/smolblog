<?php

namespace Smolblog\Core\Endpoints;

use Smolblog\Core\{Endpoint, EndpointRequest, EndpointResponse, Environment};
use Smolblog\Core\Definitions\{HttpVerb, SecurityLevel};
use Smolblog\Core\EndpointParameters\StringParameter;

class ConnectInit extends Endpoint {
	protected function initValues(): void {
		$this->route = 'connect/init/[slug]';
		$this->verbs = [HttpVerb::GET];
		$this->security = SecurityLevel::Registered;
		$this->params = new StringParameter(name: 'slug', isRequired: true);
	}

	/**
	 * Perform the action associated with this endpoint and return the response.
	 *
	 * @param EndpointRequest $request Full information of the HTTP request.
	 * @return EndpointResponse Response to give
	 */
	public function run(EndpointRequest $request): EndpointResponse {

	}
}
