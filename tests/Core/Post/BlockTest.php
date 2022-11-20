<?php

namespace Smolblog\Core\Post;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Post\Media;

final class BlockTest extends TestCase {
	public function testAudioBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\AudioBlock::class,
			new Blocks\AudioBlock(
				media: new Media(url: '/podcast/ep1.mp3', descriptiveText: 'Episode 1', attributes: [])
			)
		);
	}

	public function testBlockquoteAreaCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\BlockquoteArea::class,
			new Blocks\BlockquoteArea(
				content: [new Blocks\ParagraphBlock(content: 'Hello')]
			)
		);
	}

	public function testEmbedBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\EmbedBlock::class,
			new Blocks\EmbedBlock(
				url: 'https://www.youtube.com/watch?v=90X5NJleYJQ'
			)
		);
	}

	public function testHeadingBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\HeadingBlock::class,
			new Blocks\HeadingBlock(
				content: 'Headline'
			)
		);
	}

	public function testImageBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\ImageBlock::class,
			new Blocks\ImageBlock(
				media: new Media(url: '/art/151.jpg', descriptiveText: 'An image of the pokémon mew', attributes: [])
			)
		);
	}

	public function testLinkBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\LinkBlock::class,
			new Blocks\LinkBlock(
				url: 'https://www.oddevan.com/2019/introducing-smolblog/',
				title: 'Introducing Smolblog'
			)
		);
	}

	public function testListBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\ListBlock::class,
			new Blocks\ListBlock(
				content: '<ol><li>one</li><li>two</li></ol>',
				ordered: true
			)
		);
	}

	public function testQuoteBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\QuoteBlock::class,
			new Blocks\QuoteBlock(
				content: 'What does not kill me makes me stranger.',
				citation: '<a href="https://ozyandmillie.org/cast">Llewellyn</a>'
			)
		);
	}

	public function testReblogBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\ReblogBlock::class,
			new Blocks\ReblogBlock(
				url: 'https://www.youtube.com/watch?v=90X5NJleYJQ',
				embed: new Blocks\EmbedBlock(
					url: 'https://www.youtube.com/watch?v=90X5NJleYJQ'
				)
			)
		);
	}

	public function testVideoBlockCanBeCreated() {
		$this->assertInstanceOf(
			Blocks\VideoBlock::class,
			new Blocks\VideoBlock(
				media: new Media(url: '/vlog/grog.mp4', descriptiveText: 'Grog\'s Vlog', attributes: [])
			)
		);
	}
}
