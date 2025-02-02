<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;

return static function (MBConfig $mbConfig): void {
	$mbConfig->defaultBranch('main');

	$mbConfig->packageDirectories([
		__DIR__ . '/packages/framework',
		__DIR__ . '/packages/product',
	]);

	$mbConfig->dataToAppend([
		'require-dev' => [
			'symplify/monorepo-builder' => '^11.2',
		],
		"scripts" => [
			"test" => "phpunit --testdox --no-coverage",
			"test-fast" => "phpunit --testsuite unit --no-coverage",
			"test-coverage" => [
				"@putenv XDEBUG_MODE=coverage",
				"phpunit --testsuite unit",
				"Smolblog\\Test\\CoverageReport::report"
			],
			"lint" => "./vendor/squizlabs/php_codesniffer/bin/phpcs",
			"lintfix" => "./vendor/squizlabs/php_codesniffer/bin/phpcbf",
			"monorepo" => "./vendor/bin/monorepo-builder",
		]
	]);

	// Release workers - in order of execution.
	$mbConfig->workers([
		UpdateReplaceReleaseWorker::class,
		SetCurrentMutualDependenciesReleaseWorker::class,
		AddTagToChangelogReleaseWorker::class,
		TagVersionReleaseWorker::class,
		PushTagReleaseWorker::class,
		SetNextMutualDependenciesReleaseWorker::class,
		UpdateBranchAliasReleaseWorker::class,
		PushNextDevReleaseWorker::class,
	]);
};
