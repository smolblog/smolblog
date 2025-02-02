<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;

return static function (MBConfig $mbConfig): void {
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
};
