<?php

namespace Smolblog\Core\Definitions;

interface HttpRequest {
	public function verb(): HttpVerb;
	public function queryParams(): array;
	public function queryString(): string;
	public function url(): string;
	public function bodyString(): string;
	public function bodyJson(): array;
}
