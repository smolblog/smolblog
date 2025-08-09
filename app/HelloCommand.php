<?php

namespace Smolblog\App;

use Smolblog\Core\Content\Services\ContentTypeRegistry;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\HasConsole;

use function Tempest\Support\Arr\implode;

final class HelloCommand {
	use HasConsole;

	public function __construct(
		// private ContentTypeRegistry $contentTypes,
	) {
	}

	public static function try() {
		echo "Tried and done!\n";
	}

	#[ConsoleCommand]
	public function world(string $name = 'stranger'): void {
		$this->success("Hello, {$name}!");
	}

	#[ConsoleCommand]
	public function audit(): void {
		$this->writeln('Smolblog discovery audit:');

		$contentTypeList = $this->contentTypes->availableContentTypes();
		$this->writeln('Content Types (' . count($contentTypeList) . '):');
		$this->info(implode($this->contentTypes->availableContentTypes(), "\n"), title: 'Content Types:');
		$this->success("Done");
	}
}
