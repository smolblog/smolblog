<?php

namespace Smolblog\Markdown;

use Smolblog\Test\TestCase;

final class SmolblogMarkdownTest extends TestCase {
	private EmbedProvider $embed;
	private SmolblogMarkdown $md;

	public function setUp(): void {
		$this->embed = $this->createStub(EmbedProvider::class);
		$this->md = new SmolblogMarkdown(embedProvider: $this->embed);
	}

	public function testItParsesNormalMarkdown() {
		$test = <<<EOD
		# Markdown test #

		There comes a time when things have to happen. This is one of those times.

		## Headers

		There are [inline](https://inline.smol.blog/) links and [reference][ref] links.

		[ref]: https://ref.smol.blog/

		There may even be a way to [directly] say something?

		[directly]: https://directly.smol.blog/

		_wait wait_ this thing has **other colors**?

		### Images

		![An inline image](https://cdn.smolblog.com/inline.jpg)

		![A reference image][img]

		[img]: https://cdn.smolblog.com/ref.jpg

		I love you.
		EOD;

		$expected = <<<EOD
		<h1>Markdown test</h1>
		<p>There comes a time when things have to happen. This is one of those times.</p>
		<h2>Headers</h2>
		<p>There are <a href="https://inline.smol.blog/">inline</a> links and <a href="https://ref.smol.blog/">reference</a> links.</p>
		<p>There may even be a way to <a href="https://directly.smol.blog/">directly</a> say something?</p>
		<p><em>wait wait</em> this thing has <strong>other colors</strong>?</p>
		<h3>Images</h3>
		<p><img src="https://cdn.smolblog.com/inline.jpg" alt="An inline image"></p>
		<p><img src="https://cdn.smolblog.com/ref.jpg" alt="A reference image"></p>
		<p>I love you.</p>

		EOD;

		$this->assertEquals($expected, $this->md->parse($test));
	}

	public function testItFallsBackToALinkIfItCannotEmbed() {
		$test = <<<EOD
		# Embed test #

		You know what a really cool video is?

		:[An innocuous YouTube video](https://youtu.be/rTga41r3a4s)

		Rather unexpected, innit?
		EOD;

		$expected = <<<EOD
		<h1>Embed test</h1>
		<p>You know what a really cool video is?</p>
		<p><a href="https://youtu.be/rTga41r3a4s" target="_blank">An innocuous YouTube video</a></p>
		<p>Rather unexpected, innit?</p>

		EOD;

		$this->assertEquals($expected, $this->md->parse($test));
	}

	public function testItInsertsAnEmbedIfDirected() {
		$test = <<<EOD
		# Embed test #

		You know what a really cool video is?

		:[An innocuous YouTube video](https://youtu.be/rTga41r3a4s)

		Rather unexpected, innit?
		EOD;

		$this->embed->method('getEmbedCodeFor')->willReturn('<iframe src="https://embed.youtube.com/watch?v=rTga41r3a4s"></iframe>');

		$expected = <<<EOD
		<h1>Embed test</h1>
		<p>You know what a really cool video is?</p>
		<iframe src="https://embed.youtube.com/watch?v=rTga41r3a4s"></iframe>
		<p>Rather unexpected, innit?</p>

		EOD;

		$this->assertEquals($expected, $this->md->parse($test));
	}

	public function testCustomHandlersAreUsedIfPresent() {
		$sampleHandler = fn($block) => '<div>'.$block['content']."</div>\n";
		$this->md->addCustomCodeHandler('smol', $sampleHandler);

		$test = <<<EOD
		# Code test #

		I've got three looks.

		    Indented code block.

		```javascript
		const answer = 42;
		```

		```smol
		The smollest of blogs.
		```

		And that's it.
		EOD;

		$expected = <<<EOD
		<h1>Code test</h1>
		<p>I've got three looks.</p>
		<pre><code>Indented code block.
		</code></pre>
		<pre><code class="language-javascript">const answer = 42;
		</code></pre>
		<div>The smollest of blogs.</div>
		<p>And that's it.</p>

		EOD;

		$this->assertEquals($expected, $this->md->parse($test));
	}
}
