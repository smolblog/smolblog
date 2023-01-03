<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\Commands\BeginAuthRequest;
use Smolblog\Core\Connector\Commands\FinishAuthRequest;
use Smolblog\Core\Connector\Commands\RefreshChannels;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Events\ConnectionEstablished;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Service to handle an OAuth request with an external provider.
 */
class AuthRequestService {
	/**
	 * Construct the service
	 *
	 * @param ConnectorRegistrar   $connectors Repository of Connectors.
	 * @param AuthRequestStateRepo $stateRepo  Repository for request states.
	 * @param MessageBus           $messageBus MessageBus for the system.
	 */
	public function __construct(
		private ConnectorRegistrar $connectors,
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

		$this->stateRepo->save(new AuthRequestState(
			key: $data->state,
			userId: $request->userId,
			info: $data->info,
		));

		$request->redirectUrl = $data->url;
	}

	/**
	 * Finish the OAuth request and save the new connection and its channels.
	 *
	 * @param FinishAuthRequest $request Command to execute.
	 * @return void
	 */
	public function onFinishAuthRequest(FinishAuthRequest $request): void {
		$connector = $this->connectors->get($request->provider);
		$info = $this->stateRepo->get(key: $request->stateKey);

		$connection = $connector->createConnection(code: $request->code, info: $info);
		$this->messageBus->dispatch(new ConnectionEstablished(
			provider: $connection->provider,
			providerKey: $connection->providerKey,
			displayName: $connection->displayName,
			details: $connection->details,
			connectionId: $connection->id,
			userId: $info->userId
		));

		$this->messageBus->dispatch(new RefreshChannels(connectionId: $connection->id, userId: $info->userId));
	}
}
