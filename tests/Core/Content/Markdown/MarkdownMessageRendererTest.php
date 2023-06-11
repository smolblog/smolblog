<?php

namespace Smolblog\Core\Content\Markdown;

use Smolblog\Test\TestCase;
use Smolblog\Markdown\SmolblogMarkdown;

final class MarkdownMessageRendererTest extends TestCase {
	public function testItRenderesMarkdownOnMessagesThatNeedIt() {
		$html = '<p>Hello, world!</p>';

		$parser = $this->createStub(SmolblogMarkdown::class);
		$parser->method('parse')->willReturn($html);

		$message = $this->createMock(NeedsMarkdownRendered::class);
		$message->method('getMarkdown')->willReturn('Hello, world!');
		$message->expects($this->once())->method('setHtml')->with($this->equalTo($html));

		$service = new MarkdownMessageRenderer(md: $parser);
		$service->onNeedsMarkdownRendered($message);
	}
}
