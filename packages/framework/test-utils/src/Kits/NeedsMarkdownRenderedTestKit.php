<?php

namespace Smolblog\Test\Kits;
use PHPUnit\Framework\Attributes\TestDox;

trait NeedsMarkdownRenderedTestKit {
	#[TestDox('It implements the NeedsMarkdownRendered interface.')]
	public function testNeedsMarkdownRendered() {
		$result = [];
		$actual = $this->subject->getMarkdown();
		$this->assertIsArray($actual);
		foreach ($actual as $md) {
			$this->assertIsString($md);
			$result[] = "<div>$md</div>";
		}

		// Test this does not throw an error.
		$this->subject->setMarkdownHtml($result);
	}
}
