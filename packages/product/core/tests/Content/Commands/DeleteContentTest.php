<?php

namespace Smolblog\Core\Content\Commands;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Test\Kits\ContentTestKit;
use Smolblog\Test\TestCase;

#[CoversClass(DeleteContent::class)]
final class DeleteContentTest extends TestCase {
	use ContentTestKit;

	public function testItRequiresEditPermissionForTheContent() {
		$command = new DeleteContent(
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
