<?php

namespace Smolblog\IndieWeb\Micropub;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\ContentV1\ContentTypeRegistry;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\IndieWeb\MicroformatsConverter;

/**
 * Handle Micropub API requests.
 *
 * This is a stateless factory that creates MicropubHandler objects to handle the requests. The MicropubAdapter
 * library stores information about the request in its own state, so we have this factory service to preserve the
 * statelessness of Smolblog services.
 *
 * Since PHP essentially resets itself after every request, this is largely an academic difference. But it does keep
 * options open for using a persistant app like Laravel Octane.
 */
class MicropubService {
	/**
	 * Construct the service.
	 *
	 * @param ApiEnvironment        $env     Current environment.
	 * @param MessageBus            $bus     For sending queries and commands.
	 * @param MicroformatsConverter $mf      Handle converting Smolblog objects to their Microformats counterparts.
	 * @param ContentTypeRegistry   $typeReg For getting content type information.
	 * @param LoggerInterface       $log     Logger for debug info.
	 */
	public function __construct(
		private ApiEnvironment $env,
		private MessageBus $bus,
		private MicroformatsConverter $mf,
		private ContentTypeRegistry $typeReg,
		private LoggerInterface $log,
	) {
	}

	/**
	 * Pass a Micropub request to a new Handler.
	 *
	 * @param ServerRequestInterface $request Request to handle.
	 * @return ResponseInterface
	 */
	public function handleRequest(ServerRequestInterface $request): ResponseInterface {
		$handler = new MicropubHandler(
			env: $this->env,
			bus: $this->bus,
			mf: $this->mf,
			typeReg: $this->typeReg,
			log: $this->log,
		);
		return $handler->handleRequest($request);
	}

	/**
	 * Pass a media endpoint request to a new Handler.
	 *
	 * @param ServerRequestInterface $request Request to handle.
	 * @return ResponseInterface
	 */
	public function handleMediaEndpointRequest(ServerRequestInterface $request): ResponseInterface {
		$handler = new MicropubHandler(
			env: $this->env,
			bus: $this->bus,
			mf: $this->mf,
			typeReg: $this->typeReg,
			log: $this->log,
		);
		return $handler->handleMediaEndpointRequest($request);
	}
}
