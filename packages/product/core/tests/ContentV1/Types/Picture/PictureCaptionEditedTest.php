<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use DateTimeImmutable;
use Smolblog\Core\ContentV1\Media\Media;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\Kits\ContentEventTestKit;
use Smolblog\Test\Kits\NeedsMarkdownRenderedTestKit;
use Smolblog\Test\TestCase;

final class PictureCaptionEditedTest extends TestCase {
	use NeedsMarkdownRenderedTestKit;
	use ContentEventTestKit;

	protected function setUp(): void
	{
		$this->subject = new PictureCaptionEdited(
			caption: 'Oh. _Oh._',
			contentId: $this->randomId(true),
			userId: $this->randomId(true),
			siteId: $this->randomId(true),
			id: $this->randomId(true),
			timestamp: new DateTimeImmutable('2011-11-11 11:11:11.111'),
		);
	}
}
