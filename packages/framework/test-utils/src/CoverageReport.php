<?php

namespace Smolblog\Test;

use Closure;
use SebastianBergmann\CodeCoverage\Node\Directory;
use SebastianBergmann\CodeCoverage\Node\File;

class CoverageReport {
	public static function report() {
		$report = require __DIR__ . '/../../../../coverage.php';
		echo "Running coverage report...\n\n";

		$tests = [
			[
				'type' => 'line',
				'score' => $report->getReport()->numberOfExecutedLines() / $report->getReport()->numberOfExecutableLines(),
				'fileFilter' => fn($file) => ($file->numberOfExecutableLines() - $file->numberOfExecutedLines()) > 0
			],
		];

		$results = array_map(fn($def) => self::printReport($report, ...$def), $tests);
		if (in_array(false, $results, true)) {
			echo "\nRun `composer test-coverage` and view the HTML report in the test-coverage folder.\n\n";

			exit(1);
		}
	}

	private static function printReport($report, string $type, float $score, Closure $fileFilter): bool {
		$baseDirCharCount = strlen(dirname(dirname(dirname(__DIR__))));

		echo "Expected $type coverage: 100%\n";
		echo "  Actual $type coverage: " . floor($score * 100) . "%\n";

		if ($score >= 1) {
			echo "PASS\n\n";
			return true;
		}

		echo "\nThe following files have incomplete $type coverage:\n";
		foreach(self::getProblemFiles(
			$report->getReport(),
			$fileFilter
		) as $file) {
			echo '  ' . substr($file->pathAsString(), $baseDirCharCount) . "\n";
		}

		echo "\n";

		return false;
	}

	private static function getProblemFiles(Directory $dir, Closure $fileFilter): array {
		return array_reduce(
			$dir->directories(),
			fn($all, $subDir) => array_merge($all, self::getProblemFiles($subDir, $fileFilter)),
			array_filter($dir->files(), $fileFilter),
		);
	}

	private static function getFileMessage(File $file): string {
		$output = $file->pathAsString() . ":\n";

		foreach (array_filter($file->functions(), fn($fn) => $fn['coverage'] < 100) as $func) {
			$output .= '  Function ' . $func['functionName'] . ': ' . ($func['executable
Branches'] - $func['executedBranches']) . "\n";
		}
		foreach (array_filter($file->traits(), fn($fn) => $fn['coverage'] < 100) as $trait) {
			$output .= '  Trait ' . $trait['traitName'] . ":\n" . self::getMethodMessages($trait['methods']);
		}
		foreach (array_filter($file->classes(), fn($fn) => $fn['coverage'] < 100) as $class) {
			$output .= '  Class ' . $class['className'] . ":\n" . self::getMethodMessages($class['methods']);
		}

		$output .= "\n";
		return $output;
	}

	private static function getMethodMessages(array $methods): string {
		$output = '';
		foreach (array_filter($methods, fn($mt) => $mt['coverage'] < 100) as $method) {
			$output .= '    ' . $method['methodName'] . ': ' . ($method['executable
Branches'] - $method['executedBranches']) . "\n";
		}

		return $output;
	}
}
