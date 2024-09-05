<?php

namespace Smolblog\Core\Connection\Services;

use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Connection\Commands\BeginAuthRequest;
use Smolblog\Core\Connection\Commands\FinishAuthRequest;
use Smolblog\Core\Connection\ConnectionHandler;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\Core\Connection\Events\ConnectionEstablished;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\ServiceNotRegistered;
use Smolblog\Foundation\Service\Command\CommandHandler;
use Smolblog\Foundation\Service\Command\CommandHandlerService;

/**
 * Service to handle an OAuth request with an external provider.
 */
class AuthRequestService implements CommandHandlerService {
	/**
	 * Construct the service.
	 *
	 * @param ConnectionHandlerRegistry $handlers  Get Connection handlers.
	 * @param AuthRequestStateRepo      $stateRepo Save state between requests.
	 * @param EventDispatcherInterface  $eventBus  Save the final Connection.
	 */
	public function __construct(
		private ConnectionHandlerRegistry $handlers,
		private AuthRequestStateRepo $stateRepo,
		private EventDispatcherInterface $eventBus,
	) {
	}

	/**
	 * Start an OAuth request and provide the URL to redirect the user to.
	 *
	 * Sets the `redirectUrl` property on the command to the URL the end-user should be given to start the process.
	 *
	 * @throws ServiceNotRegistered When no service is registered with the given key.
	 *
	 * @param BeginAuthRequest $request Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onBeginAuthRequest(BeginAuthRequest $request): void {
		$connector = $this->handlers->get($request->provider);

		$data = $connector->getInitializationData(callbackUrl: $request->callbackUrl);

		$this->stateRepo->saveAuthRequestState(new AuthRequestState(
			key: $data->state,
			userId: $request->userId,
			provider: $request->provider,
			info: $data->info,
			returnToUrl: $request->returnToUrl,
		));

		$request->setReturnValue($data->url);
	}

	/**
	 * Finish the OAuth request and save the new connection and its channels.
	 *
	 * @throws ServiceNotRegistered When no service is registered with the given key.
	 *
	 * @param FinishAuthRequest $request Command to execute.
	 * @return void
	 */
	#[CommandHandler]
	public function onFinishAuthRequest(FinishAuthRequest $request): void {
		$connector = $this->handlers->get($request->provider);

		$info = $this->stateRepo->getAuthRequestState(key: $request->stateKey);
		if (!isset($info)) {
			throw new EntityNotFound(entityId: $request->stateKey, entityName: AuthRequestState::class);
		}

		$connection = $connector->createConnection(code: $request->code, info: $info);

		$this->eventBus->dispatch(new ConnectionEstablished(
			provider: $connection->provider,
			providerKey: $connection->providerKey,
			displayName: $connection->displayName,
			details: $connection->details,
			entityId: $connection->getId(),
			userId: $info->userId
		));

		$request->setReturnValue($info->returnToUrl);
	}
}
