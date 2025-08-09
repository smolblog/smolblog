<?php

use Tempest\Core\DiscoveryConfig;

return new DiscoveryConfig()->skipPaths(
	__DIR__ . '/../../packages/framework/activitypub/src/',
	__DIR__ . '/../../packages/framework/api/src/',
	__DIR__ . '/../../packages/framework/infrastructure/src/',
	__DIR__ . '/../../packages/framework/markdown/src/',
	__DIR__ . '/../../packages/framework/test-utils/src/',
	__DIR__ . '/../../packages/product/core-rest-api/src/',
	__DIR__ . '/../../packages/product/core-test-utils/src/'
);
