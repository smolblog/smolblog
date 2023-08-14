<?php

namespace Smolblog\Core\Content\Types\Picture;

use DateTimeImmutable;
use Smolblog\Core\Content\Media\Media;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\ContentEventTestKit;
use Smolblog\Test\NeedsMarkdownRenderedTestKit;
use Smolblog\Test\NeedsMediaObjectsTestKit;
use Smolblog\Test\NeedsMediaRenderedTestKit;
use Smolblog\Test\TestCase;

final class PictureMediaEditedTest extends TestCase {
	use NeedsMediaObjectsTestKit;
	use NeedsMediaRenderedTestKit;
	use ContentEventTestKit;

	protected function setUp(): void
	{
		$this->subject = new PictureMediaEdited(
			mediaIds: [
				$this->randomId(true),
				$this->randomId(true),
				$this->randomId(true),
			],
			contentId: $this->randomId(true),
			userId: $this->randomId(true),
			siteId: $this->randomId(true),
			id: $this->randomId(true),
			timestamp: new DateTimeImmutable('2011-11-11 11:11:11.111'),
		);
	}
}
