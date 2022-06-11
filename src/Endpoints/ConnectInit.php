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
		$env = Environment::get();
		$providerSlug = $request->params['slug'];

		$connector = ConnectorRegistrar::retrieve($providerSlug);
		$data = $connector->getInitializationData($env->getFullRestUrl("connect/callback/$providerSlug"));

		$info = [
			...$data->info,
			'user_id' => $request->user->id,
		];
		$env->setTransient(name: $data->state, value: $info, secondsUntilExpiration: 300);

		return new EndpointResponse(statusCode: 200, body: ['authUrl' => $data->url]);
	}
}
