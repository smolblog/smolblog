<?php

namespace Smolblog\Core\ContentV1\Types\Reblog;

use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

final class ReblogTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$this->subject = new Reblog(
			url: '//echoing.green/',
			comment: 'You should check this out.',
		);
	}

	public function testContentInfoCanBeSerializedAndDeserialized() {
		$object = new Reblog(
			url: '//echoing.green',
			info: new ExternalContentInfo(
				title: 'The Echoing Green',
				embed: '<iframe src="//youtu.be/254"></iframe>',
			),
		);

		$this->assertEquals($object, Reblog::fromArray($object->toArray()));
	}

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
		$this->assertEquals("<p>Bamboozled again...</p>\n", $reblog->getCommentHtml());
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
