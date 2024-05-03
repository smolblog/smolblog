<?php

namespace Smolblog\Core\Content\Commands;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Test\Kits\ContentTestKit;
use Smolblog\Test\TestCase;

#[CoversClass(UpdateContent::class)]
final class UpdateContentTest extends TestCase {
	use ContentTestKit;
	public function testItRequiresAuthorPermissionForTheSite() {
		$command = new UpdateContent(
			userId: $this->randomId(),
			content: $this->sampleContent(),
		);

		$expected = new UserCanEditContent(
			userId: $command->userId,
			contentId: $command->content->id,
		);

		$this->assertEquals($expected, $command->getAuthorizationQuery());
	}
}
