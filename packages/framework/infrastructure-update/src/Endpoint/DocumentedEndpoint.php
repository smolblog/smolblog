<?php

namespace Smolblog\Infrastructure\Endpoint;

interface DocumentedEndpoint extends Endpoint {
	public function getDocumentation(): EndpointDocumentation;
}
