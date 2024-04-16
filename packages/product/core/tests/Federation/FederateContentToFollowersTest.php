<?php

namespace Smolblog\Core\Federation;

use DateTimeImmutable;
use Smolblog\Core\Content;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\GenericContent;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

final class FederateContentToFollowersTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void
	{
		$siteId = $this->randomId(true);

		$this->subject = new FederateContentToFollowers(
			content: new Content(
				id: $this->randomId(true),
				body: new GenericContent('test', '<p>something</p>'),
				siteId: $siteId,
				authorId: $this->randomId(true),
				published: true,
			),
			followers: [
				new Follower(
					siteId: $siteId,
					provider: 'test',
					providerKey: '123',
					displayName: 'A',
					details: ['one' => 'two']
				),
				new Follower(
					siteId: $siteId,
					provider: 'test',
					providerKey: '456',
					displayName: 'B',
					details: ['three' => 'four']
				),
			],
			provider: 'test',
		);
	}

	public function testFollowersMustNotBeEmpty() {
		$this->expectException(InvalidCommandParametersException::class);

		FederateContentToFollowers::deserializeValue([
			...$this->subject->serializeValue(),
			'followers' => []
		]);
	}

	public function testFollowersMustMatchProvider() {
		$this->expectException(InvalidCommandParametersException::class);

		FederateContentToFollowers::deserializeValue([
			...$this->subject->serializeValue(),
			'provider' => 'wrong'
		]);
	}
}
