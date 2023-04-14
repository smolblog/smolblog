<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class SyndicationLinksTest extends TestCase {
	public function testItWillSerializeCorrectly() {
		$serial = [
			'links' => [
				['url' => '//one.com/'],
				['url' => '//two.net/'],
				['url' => '//six.org/', 'channelId' => 'f5baa76f-50ac-4bbc-8aae-2e9660b6bb54'],
			],
			'channels' => []
		];

		$object = new Syndication(
			links: [
				new SyndicationLink(url: '//one.com/'),
				new SyndicationLink(url: '//two.net/'),
				new SyndicationLink(url: '//six.org/', channelId: Identifier::fromString('f5baa76f-50ac-4bbc-8aae-2e9660b6bb54')),
			],
			channels: []
		);

		$this->assertEquals($serial, $object->toArray());
		$this->assertEquals($object, Syndication::fromArray($serial));
	}
}
