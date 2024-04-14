<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Test\TestCase;

final class EditMediaAttributesTest extends TestCase {
	public function testItRequiresEitherTitleOrAltText() {
		$this->expectException(InvalidCommandParametersException::class);

		new EditMediaAttributes(
			contentId: $this->randomId(),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
	}
}
