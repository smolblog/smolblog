<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Environment;

/**
 * Service to begin an OAuth request with an external provider.
 */
class AuthRequestInitializer {
	/**
	 * Construct the service
	 *
	 * @param ConnectorRegistrar     $connectors ConnectorRegistrar loaded with Connectors.
	 * @param AuthRequestStateWriter $stateSaver Writable service for AuthRequestStates.
	 */
	public function __construct(
		private ConnectorRegistrar $connectors,
		private AuthRequestStateWriter $stateSaver,
	) {
	}

	/**
	 * Start an OAuth request and return the URL to redirect the user to.
	 *
	 * @param BeginAuthRequest $request Command to execute.
	 * @return string URL to redirect the user to.
	 */
	public function handleBeginAuthRequest(BeginAuthRequest $request): string {
		$connector = $this->connectors->get($request->provider);

		$data = $connector->getInitializationData(callbackUrl: $request->callbackUrl);

		$this->stateSaver->save(new AuthRequestState(
			id: $data->state,
			userId: $request->userId,
			info: $data->info,
		));

		return $data->url;
	}
}
