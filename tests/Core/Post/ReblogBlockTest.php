<?php

namespace Smolblog\Core\Post\Blocks;

use PHPUnit\Framework\TestCase;

final class ReblogBlockTest extends TestCase {
	public function testReblogBlockCanBeCreatedWithOnlyAUrl() {
		$block = new ReblogBlock(url: 'https://www.youtube.com/watch?v=90X5NJleYJQ');

		$this->assertInstanceOf(ReblogBlock::class, $block);
		$this->assertInstanceOf(EmbedBlock::class, $block->embed);
		$this->assertTrue($block->showEmbed);
	}

	public function testReblogBlockCanBeCreatedWithALink() {
		$linkBlock = new LinkBlock(
			url: 'https://www.youtube.com/watch?v=90X5NJleYJQ',
			title: "I said consummate V's!"
		);
		$block = new ReblogBlock(
			url: 'https://www.youtube.com/watch?v=90X5NJleYJQ',
			showEmbed: false,
			link: $linkBlock,
		);

		$this->assertInstanceOf(ReblogBlock::class, $block);
		$this->assertInstanceOf(EmbedBlock::class, $block->embed);
		$this->assertInstanceOf(LinkBlock::class, $block->link);
		$this->assertFalse($block->showEmbed);
	}

	public function testReblogBlockWillShowEmbedIfNoLink() {
		$block = new ReblogBlock(
			url: 'https://www.youtube.com/watch?v=90X5NJleYJQ',
			showEmbed: false,
		);

		$this->assertInstanceOf(ReblogBlock::class, $block);
		$this->assertInstanceOf(EmbedBlock::class, $block->embed);
		$this->assertNull($block->link);
		$this->assertTrue($block->showEmbed);
	}
}
