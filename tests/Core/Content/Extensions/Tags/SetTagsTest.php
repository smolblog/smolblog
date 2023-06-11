<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Test\TestCase;
use Smolblog\Core\Content\Queries\UserCanEditContent;
use Smolblog\Framework\Objects\Identifier;

final class SetTagsTest extends TestCase {
	public function testItRequiresEditPermissions() {
		$siteId = Identifier::fromString('b181e363-abb9-4c0a-852a-86afcfcccdff');
		$userId = Identifier::fromString('f2dfc1e7-09d3-413c-8531-a1a32c5d3739');
		$contentId = Identifier::fromString('c139e180-e0b6-47d6-af10-9461ec9ec293');

		$command = new SetTags(
			siteId: $siteId,
			userId: $userId,
			contentId: $contentId,
			tags: ['one', 'two'],
		);

		$this->assertEquals(
			new UserCanEditContent(userId: $userId, siteId: $siteId, contentId: $contentId),
			$command->getAuthorizationQuery()
		);
	}
}
