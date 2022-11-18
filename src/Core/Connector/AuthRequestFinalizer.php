<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\{Executor, Service};

/**
 * Service to finish an OAuth request with an external provider.
 */
class AuthRequestFinalizer implements Service {
	/**
	 * Create the Service
	 *
	 * @param ConnectorRegistrar     $connectors     Connector Registrar.
	 * @param AuthRequestStateReader $stateRepo      State repository.
	 * @param ConnectionWriter       $connectionRepo Connection repository.
	 * @param Executor               $commands       Command Bus.
	 */
	public function __construct(
		private ConnectorRegistrar $connectors,
		private AuthRequestStateReader $stateRepo,
		private ConnectionWriter $connectionRepo,
		private Executor $commands,
	) {
	}

	/**
	 * Start an OAuth request and return the URL to redirect the user to.
	 *
	 * @param FinishAuthRequest $request Command to execute.
	 * @return void
	 */
	public function run(FinishAuthRequest $request): void {
		$connector = $this->connectors->get($request->provider);
		$info = $this->stateRepo->get(id: $request->stateKey);

		$connection = $connector->createConnection(code: $request->code, info: $info);
		$this->connectionRepo->save(connection: $connection);

		$this->commands->exec(new RefreshChannels($connection->id));
	}
}
