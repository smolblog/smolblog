<?php

namespace Smolblog\Infrastructure\Endpoint\OpenApi;

enum ParameterIn: string {
	case Query = 'query';
	case Header = 'header';
	case Path = 'path';
	case Cookie = 'cookie';
}
