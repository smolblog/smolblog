<?php

namespace Smolblog\Core\Federation;

use DateTimeImmutable;
use Smolblog\Core\ContentV1\Content;
use Smolblog\Core\ContentV1\ContentType;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Events\PublicContentAdded;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Test\Kits\MessageBusMockKit;
use Smolblog\Test\TestCase;

final class FederationServiceTest extends TestCase {
	use MessageBusMockKit;

	protected FollowerProvider $mockProvider;
	protected MessageBus $mockBus;

	protected function setUp(): void {
		$this->mockBus = $this->createMock(MessageBus::class);
		$this->mockProvider = $this->createMock(FollowerProvider::class);

		$reg = $this->createStub(FollowerProviderRegistry::class);
		$reg->method('get')->willReturn($this->mockProvider);

		$this->subject = new FederationService(bus: $this->mockBus, followerProviders: $reg);
	}

	public function testItDispatchesAnAsyncCommandForEachFollowerProviderWhenContentIsPublished() {
		$content = new Content(
			type: $this->createStub(ContentType::class),
			siteId: $this->randomId(true),
			authorId: $this->randomId(),
			permalink: '//smol.blog/543',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
		);

		$abcFollowers = [
			new Follower(siteId: $content->siteId, provider: 'abc', providerKey: '123', displayName: '123', details: []),
			new Follower(siteId: $content->siteId, provider: 'abc', providerKey: '456', displayName: '456', details: []),
		];
		$xyzFollowers = [
			new Follower(siteId: $content->siteId, provider: 'xyz', providerKey: '345', displayName: '345', details: []),
			new Follower(siteId: $content->siteId, provider: 'xyz', providerKey: '678', displayName: '678', details: []),
		];

		$event = $this->createStub(PublicContentAdded::class);
		$event->method('getContent')->willReturn($content);
		$this->mockBus->method('fetch')->willReturn(['abc' => $abcFollowers, 'xyz' => $xyzFollowers]);


		$this->messageBusShouldDispatchAsync($this->mockBus,
			$this->equalTo(new FederateContentToFollowers(
				content: $content,
				followers: $abcFollowers,
				provider: 'abc',
			)),
			$this->equalTo(new FederateContentToFollowers(
				content: $content,
				followers: $xyzFollowers,
				provider: 'xyz',
			)),
		);

		$this->subject->onPublicContentAdded($event);
	}

	public function testItDelegatesFollowersToTheirProvider() {
		$content = new Content(
			type: $this->createStub(ContentType::class),
			siteId: $this->randomId(true),
			authorId: $this->randomId(),
			permalink: '//smol.blog/543',
			publishTimestamp: new DateTimeImmutable(),
			visibility: ContentVisibility::Published,
		);
		$abcFollowers = [
			new Follower(siteId: $content->siteId, provider: 'abc', providerKey: '123', displayName: '123', details: []),
			new Follower(siteId: $content->siteId, provider: 'abc', providerKey: '456', displayName: '456', details: []),
		];

		$command = new FederateContentToFollowers(
			content: $content,
			followers: $abcFollowers,
			provider: 'abc',
		);

		$this->mockProvider->expects($this->once())->method('sendContentToFollowers')->with(
			content: $this->equalTo($content),
			followers: $this->equalTo($abcFollowers),
		);

		$this->subject->onFederateContentToFollowers($command);
	}
}
