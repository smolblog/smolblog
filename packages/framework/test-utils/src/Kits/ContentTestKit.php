<?php

namespace Smolblog\Test\Kits;

use Smolblog\Core\Content;
use Smolblog\Core\Content\Type\ContentType;
use Smolblog\Foundation\Value\Fields\Identifier;

trait ContentTestKit {
	/**
	 * Get a basic piece of content.
	 *
	 * @param mixed ...$overrides Override individual fields.
	 * @return Content
	 */
	private function sampleContent(mixed ...$overrides): Content {
		$basic = new Content(
			body: new readonly class() extends ContentType {
				public function __construct(public string $word = 'hello') {}
				public function getTitle(): string { return $this->word; }
			},
			siteId: Identifier::fromString('03897791-bf43-4d29-b6d3-fc0fc155b6c2'),
			authorId: Identifier::fromString('6b0fe221-0471-4e6d-8ae6-1becfcf322d3'),
			id: Identifier::fromString('41e9f91b-273b-44ce-842f-f4171997c49f'),
		);

		return $basic->with(...$overrides);
	}
}
