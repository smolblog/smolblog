<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;

return static function (MBConfig $mbConfig): void {
	$mbConfig->packageDirectories([
		__DIR__ . '/packages/framework',
		__DIR__ . '/packages/product',
		__DIR__ . '/application'
	]);

	$mbConfig->dataToAppend([
		ComposerJsonSection::AUTOLOAD_DEV => [
			'psr-4' => [
				'Smolblog\\Mock\\' => 'mock-app/'
			],
		],
	]);
};
