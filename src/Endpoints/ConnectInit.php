<?php

namespace Smolblog\Core\Endpoints;

use Smolblog\Core\{Endpoint, EndpointRequest, EndpointResponse, Environment};
use Smolblog\Core\Definitions\{HttpVerb, SecurityLevel};
use Smolblog\Core\EndpointParameters\ConnectorSlug;
use Smolblog\Core\Registrars\ConnectorRegistrar;

class ConnectInit extends Endpoint {
	protected function initValues(): void {
		$this->route = 'connect/init/[slug]';
		$this->verbs = [HttpVerb::GET];
		$this->security = SecurityLevel::Registered;
		$this->params = [new ConnectorSlug(name: 'slug', isRequired: true)];
	}

	/**
	 * Perform the action associated with this endpoint and return the response.
	 *
	 * @param EndpointRequest $request Full information of the HTTP request.
	 * @return EndpointResponse Response to give
	 */
	public function run(EndpointRequest $request): EndpointResponse {
		$connector = ConnectorRegistrar::retrieve($request->params['slug']);
		$data = $connector->getInitializationData();

		$info = [
			...$data['info'],
			'user' => $request->user->id,
		];
		Environment::get()->setTransient(name: $data['key'], value: $info, secondsUntilExpiration: 300);

		return new EndpointResponse(statusCode: 200, body: ['authUrl' => $info['url']]);
	}
}
