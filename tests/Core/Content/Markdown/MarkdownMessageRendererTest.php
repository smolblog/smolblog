<?php

namespace Smolblog\Core\ContentV1\Markdown;

use Smolblog\Test\TestCase;
use Smolblog\Markdown\SmolblogMarkdown;

final class MarkdownMessageRendererTest extends TestCase {
	public function testItRenderesMarkdownOnMessagesThatNeedIt() {
		$md1 = 'Hello, world!';
		$md2 = 'This is a website.';

		$html1 = '<p>Hello, world!</p>';
		$html2 = '<p>This is a website.</p>';

		$parser = $this->createStub(SmolblogMarkdown::class);
		$parser->method('parse')->willReturnCallback(fn($md) => match ($md) { $md1 => $html1, $md2 => $html2 });

		$message = $this->createMock(NeedsMarkdownRendered::class);
		$message->method('getMarkdown')->willReturn([$md1, $md2]);
		$message->expects($this->once())->method('setMarkdownHtml')->with($this->equalTo([$html1, $html2]));

		$service = new MarkdownMessageRenderer(md: $parser);
		$service->onNeedsMarkdownRendered($message);
	}
}
