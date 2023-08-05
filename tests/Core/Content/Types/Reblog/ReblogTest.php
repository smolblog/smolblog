<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Test\TestCase;

final class ReblogTest extends TestCase {
	public function testTheTitleIsTheExternalContentTitle() {
		$external = new ExternalContentInfo(
			title: 'An innocuous YouTube video',
			embed: '<iframe src="//youtu.be/video/embed"></iframe>',
		);

		$reblog = new Reblog(
			url: 'https://youtu.be/abc123',
			info: $external,
		);

		$this->assertEquals('An innocuous YouTube video', $reblog->getTitle());
	}

	public function testTheCommentIsOptional() {
		$external = new ExternalContentInfo(
			title: 'An innocuous YouTube video',
			embed: '<iframe src="//youtu.be/video/embed"></iframe>',
		);

		$reblog = new Reblog(
			url: 'https://youtu.be/abc123',
			info: $external,
		);

		$this->assertEquals($external->embed . "\n\n", $reblog->getBodyContent());
	}

	public function testTheBodyIsTheEmbedPlusComment() {
		$external = new ExternalContentInfo(
			title: 'An innocuous YouTube video',
			embed: '<iframe src="//youtu.be/video/embed"></iframe>',
		);

		$reblog = new Reblog(
			url: 'https://youtu.be/abc123',
			info: $external,
			comment: 'Bamboozled again...',
			commentHtml: "<p>Bamboozled again...</p>\n",
		);

		$this->assertEquals(
			'<iframe src="//youtu.be/video/embed"></iframe>' . "\n\n<p>Bamboozled again...</p>\n",
			$reblog->getBodyContent()
		);
	}

	public function testExternalInfoAndCommentHtmlCanBeAddedLater() {
		$external = new ExternalContentInfo(
			title: 'An innocuous YouTube video',
			embed: '<iframe src="//youtu.be/video/embed"></iframe>',
		);

		$reblog = new Reblog(
			url: 'https://youtu.be/abc123',
			comment: 'Bamboozled again...',
		);

		$reblog->setCommentHtml("<p>Bamboozled again...</p>\n");
		$reblog->setExternalInfo($external);

		$this->assertEquals(
			'<iframe src="//youtu.be/video/embed"></iframe>' . "\n\n<p>Bamboozled again...</p>\n",
			$reblog->getBodyContent()
		);
	}

	public function testItsTypeKeyIsReblog() {
		$this->assertEquals('reblog', (new Reblog('a'))->getTypeKey());
	}
}
