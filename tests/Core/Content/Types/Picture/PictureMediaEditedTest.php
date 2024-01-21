<?php

namespace Smolblog\Core\Content\Types\Picture;

use DateTimeImmutable;
use Smolblog\Core\Content\Media\Media;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\Kits\ContentEventTestKit;
use Smolblog\Test\Kits\NeedsMarkdownRenderedTestKit;
use Smolblog\Test\Kits\NeedsMediaObjectsTestKit;
use Smolblog\Test\Kits\NeedsMediaRenderedTestKit;
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

	public function testItWillStripArrayKeys() {
		$event = new PictureMediaEdited(
			mediaIds: [
				'photo' => Identifier::fromString('d2655068-427e-40e3-bbd4-83154da2d443'),
				'image' => Identifier::fromString('9fcfde4b-63f6-41a1-a521-d2bcd7e50ffb'),
			],
			contentId: $this->randomId(true),
			userId: $this->randomId(true),
			siteId: $this->randomId(true),
			id: $this->randomId(true),
			timestamp: new DateTimeImmutable('2011-11-11 11:11:11.111'),
		);

		$this->assertEquals([
			Identifier::fromString('d2655068-427e-40e3-bbd4-83154da2d443'),
			Identifier::fromString('9fcfde4b-63f6-41a1-a521-d2bcd7e50ffb'),
		], $event->mediaIds);
	}
}
