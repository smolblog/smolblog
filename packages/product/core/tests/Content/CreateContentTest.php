<?php

namespace Smolblog\Core\Content\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Content\Events\ContentCreated;
use Smolblog\Test\ContentTestBase;
use Smolblog\Test\TestDefaultContentType;

final class CreateContentTest extends ContentTestBase {
	public function testTypeWithDefaultService() {
		$contentId = $this->randomId();
		$command = new CreateContent(
			body: new TestDefaultContentType(title: 'Default', body: 'I got the email; you got the email.'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
			contentId: $contentId,
		);

		$this->expectEvent(new ContentCreated(
			body: $command->body,
			aggregateId: $command->siteId,
			userId: $command->userId,
			entityId: $contentId,
			contentUserId: $command->userId,
		));

		$this->app->execute($command);
	}
}
