<?php

namespace Smolblog\Core;

class ConnectorInitData {
	public readonly string $url;
	public readonly string $state;
	public readonly array $info;

	public function __construct(string $url, string $state, array $info) {
		$this->url = $url;
		$this->state = $state;
		$this->info = $info;
	}
}
