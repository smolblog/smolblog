<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\Commands\BeginAuthRequest;
use Smolblog\Core\Connector\Commands\FinishAuthRequest;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Events\ConnectionEstablished;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

/**
 * Service to handle an OAuth request with an external provider.
 */
class AuthRequestService implements Listener {
	/**
	 * Construct the service
	 *
	 * @param ConnectorRegistry    $connectors Repository of Connectors.
	 * @param AuthRequestStateRepo $stateRepo  Repository for request states.
	 * @param MessageBus           $messageBus MessageBus for the system.
	 */
	public function __construct(
		private ConnectorRegistry $connectors,
		private AuthRequestStateRepo $stateRepo,
		private MessageBus $messageBus,
	) {
	}

	/**
	 * Start an OAuth request and provide the URL to redirect the user to.
	 *
	 * Sets the `redirectUrl` property on the command to the URL the end-user should be given to start the process.
	 *
	 * @param BeginAuthRequest $request Command to execute.
	 * @return void
	 */
	public function onBeginAuthRequest(BeginAuthRequest $request): void {
		$connector = $this->connectors->get($request->provider);

		$data = $connector->getInitializationData(callbackUrl: $request->callbackUrl);

		$this->stateRepo->saveAuthRequestState(new AuthRequestState(
			key: $data->state,
			userId: $request->userId,
			provider: $request->provider,
			info: $data->info,
			returnToUrl: $request->returnToUrl,
		));

		$request->setRedirectUrl($data->url);
	}

	/**
	 * Finish the OAuth request and save the new connection and its channels.
	 *
	 * @param FinishAuthRequest $request Command to execute.
	 * @return void
	 */
	public function onFinishAuthRequest(FinishAuthRequest $request): void {
		$connector = $this->connectors->get($request->provider);
		$info = $this->stateRepo->getAuthRequestState(key: $request->stateKey);

		$connection = $connector->createConnection(code: $request->code, info: $info);
		$this->messageBus->dispatch(new ConnectionEstablished(
			provider: $connection->provider,
			providerKey: $connection->providerKey,
			displayName: $connection->displayName,
			details: $connection->details,
			connectionId: $connection->getId(),
			userId: $info->userId
		));

		$request->setReturnUrl($info->returnToUrl);
	}
}
