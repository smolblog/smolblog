<?php

namespace Smolblog\Core\Content\Types\Picture;

use DateTimeImmutable;
use Smolblog\Core\Content\Media\Media;
use Smolblog\Foundation\Value\Fields\Identifier;
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
