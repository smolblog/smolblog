<?php

namespace Smolblog\Core\Connection\Services;

use Cavatappi\Foundation\Command\CommandHandler;
use Cavatappi\Foundation\Command\CommandHandlerService;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Core\Connection\Commands\BeginAuthRequest;
use Smolblog\Core\Connection\Commands\FinishAuthRequest;
use Smolblog\Core\Connection\Data\AuthRequestStateRepo;
use Smolblog\Core\Connection\Entities\AuthRequestState;
use Smolblog\Core\Connection\Events\ConnectionEstablished;

/**
 * Service to handle an OAuth request with an external handler.
 */
class AuthRequestService implements CommandHandlerService {
	/**
	 * Construct the service.
	 *
	 * @param ConnectionHandlerRegistry  $handlers  Get Connection handlers.
	 * @param AuthRequestStateRepo       $stateRepo Save state between requests.
	 * @param EventDispatcherInterface   $eventBus  Save the final Connection.
	 * @param ConnectionChannelRefresher $refresher Get the latest channels.
	 */
	public function __construct(
		private ConnectionHandlerRegistry $handlers,
		private AuthRequestStateRepo $stateRepo,
		private EventDispatcherInterface $eventBus,
		private ConnectionChannelRefresher $refresher,
	) {}

	/**
	 * Start an OAuth request and provide the URL to redirect the user to.
	 *
	 * Sets the `redirectUrl` property on the command to the URL the end-user should be given to start the process.
	 *
	 * @throws ServiceNotRegistered When no service is registered with the given key.
	 *
	 * @param BeginAuthRequest $request Command to execute.
	 * @return string
	 */
	#[CommandHandler]
	public function onBeginAuthRequest(BeginAuthRequest $request): string {
		$connector = $this->handlers->get($request->handler);

		$data = $connector->getInitializationData(callbackUrl: $request->callbackUrl);

		$this->stateRepo->saveAuthRequestState(new AuthRequestState(
			key: $data->state,
			userId: $request->userId,
			handler: $request->handler,
			info: $data->info,
			returnToUrl: $request->returnToUrl,
		));

		return $data->url;
	}

	/**
	 * Finish the OAuth request and save the new connection and its channels.
	 *
	 * @throws EntityNotFound When no existing request is found for the given key.
	 *
	 * @param FinishAuthRequest $request Command to execute.
	 * @return string|null
	 */
	#[CommandHandler]
	public function onFinishAuthRequest(FinishAuthRequest $request): ?string {
		$connector = $this->handlers->get($request->handler);

		$info = $this->stateRepo->getAuthRequestState(key: $request->stateKey);
		if (!isset($info)) {
			throw new EntityNotFound(entityId: $request->stateKey, entityName: AuthRequestState::class);
		}

		$connection = $connector->createConnection(code: $request->code, info: $info);

		$this->eventBus->dispatch(new ConnectionEstablished(
			handler: $connection->handler,
			handlerKey: $connection->handlerKey,
			displayName: $connection->displayName,
			details: $connection->details,
			userId: $info->userId,
		));

		// Get the latest list of channels.
		$this->refresher->refresh(connection: $connection, userId: $info->userId);

		return $info->returnToUrl;
	}
}
