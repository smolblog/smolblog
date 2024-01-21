<?php

namespace Smolblog\Test;

use SebastianBergmann\CodeCoverage\Node\Directory;
use SebastianBergmann\CodeCoverage\Node\File;

class CoverageReport {
	public static function report() {
		$report = require __DIR__ . '/../coverage.php';
		$baseDirCharCount = strlen(dirname(__DIR__) . '/src/');

		$score = $report->getReport()->numberOfExecutedBranches() / $report->getReport()->numberOfExecutableBranches();

		echo "Expected branch coverage: 100%\n";
		echo '  Actual branch coverage: ' . floor($score * 100) . "%\n";

		if ($score >= 1) {
			echo "PASS\n\n";
			return;
		}

		echo "\nThe following files have incomplete branch coverage:\n";
		foreach(self::getProblemFiles($report->getReport()) as $file) {
			echo '  ' . substr($file->pathAsString(), $baseDirCharCount) . "\n";
		}

		echo "\nRun `composer test-coverage` and view the HTML report in the test-coverage folder.\n\n";

		exit(1);
	}

	private static function getProblemFiles(Directory $dir): array {
		return array_reduce(
			$dir->directories(),
			fn($all, $subDir) => array_merge($all, self::getProblemFiles($subDir)),
			array_filter(
				$dir->files(),
				fn($file) => ($file->numberOfExecutableBranches() - $file->numberOfExecutedBranches()) > 0
			),
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
