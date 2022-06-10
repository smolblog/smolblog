<?php

namespace Smolblog\Core;

use Smolblog\Core\Definitions\HttpVerb;
use Smolblog\Core\Models\ExternalCredential;

/**
 * Class to handle authenticating against an external OAuth provider.
 */
abstract class Connector {
	/**
	 * Identifier for the provider.
	 *
	 * @var string
	 */
	public string $slug;

	protected ConnectionProvider $provider;

	/**
	 * Create the Authenticator
	 *
	 * @param string $slug Identifier for the provider.
	 */
	public function __construct(ConnectionProvider $provider, string $slug = null) {
		$this->slug = $slug ?? $provider->slug();
		$this->provider = $provider;
	}

	/**
	 * Get a URL to start an OAuth session with the provider
	 *
	 * @param RequestContext $context Context for this request.
	 * @return string URL to send the user to.
	 */
	abstract public function getInitializationUrl(RequestContext $context): string;

	/**
	 * Handle the OAuth callback from the provider and create the credential
	 *
	 * @param EndpointRequest $request Information sent from the provider.
	 * @return ExternalCredential Created credentials
	 */
	abstract public function handleCallback(EndpointRequest $request): ExternalCredential;

	/**
	 * Make a request to the external API with the given information.
	 *
	 * @param string                  $againstEndpoint Endpoint of the external API to hit.
	 * @param HttpVerb                $withVerb        HTTP method to use; defaults to GET.
	 * @param ExternalCredential|null $withAccount     Account to use to authorize the request if needed.
	 * @param string|null             $withBody        Body of the request if needed.
	 * @return EndpointResponse Response from the external API.
	 */
	abstract public function makeAuthenticatedRequest(
		string $againstEndpoint,
		HttpVerb $withVerb = HttpVerb::GET,
		ExternalCredential $withAccount = null,
		string $withBody = null
	): EndpointResponse;
}
