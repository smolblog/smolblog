<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect()) // @TODO 4.0 no need to call this manually
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS3x0' => true,
        '@PER-CS3x0:risky' => true,
				'array_syntax' => ['syntax' => 'short'],
				'braces_position' => [
					'anonymous_classes_opening_brace' => 'same_line',
					'anonymous_functions_opening_brace' => 'same_line',
					'classes_opening_brace' => 'same_line',
					'control_structures_opening_brace' => 'same_line',
					'functions_opening_brace' => 'same_line',
				],
				'method_argument_space' => [
					'attribute_placement' => 'same_line',
				],
    ])
    // 💡 by default, Fixer looks for `*.php` files excluding `./vendor/` - here, you can groom this config
    ->setFinder(
        (new Finder())
            // 💡 root folder to check
            ->in(__DIR__ . '/packages')
            // 💡 additional files, eg bin entry file
            // ->append([__DIR__.'/bin-entry-file'])
            // 💡 folders to exclude, if any
            // ->exclude([/* ... */])
            // 💡 path patterns to exclude, if any
            // ->notPath([/* ... */])
            // 💡 extra configs
            // ->ignoreDotFiles(false) // true by default in v3, false in v4 or future mode
            // ->ignoreVCS(true) // true by default
    )
		->setIndent("\t") // I will 🤬ing die on this 🤬ing hill.
;
