<?php

namespace Smolblog\Core\Media\Commands;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Core\Media\Events\MediaCreated;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Foundation\Utilities\HttpVerb;
use Cavatappi\Test\Kits\HttpMessageComparisonTestKit;
use Nyholm\Psr7\Response;
use Smolblog\Core\Test\MediaTestBase;
use Smolblog\Core\Test\Stubs\ExampleFiles;

#[AllowMockObjectsWithoutExpectations]
final class SideloadMediaTest extends MediaTestBase {
	use HttpMessageComparisonTestKit;

	public function testHappyPath() {
		$mediaId = $this->randomId();
		$command = new SideloadMedia(
			url: HttpMessageFactory::uri('https://cdn.smol.blog/site/image.png'),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'Image for testing',
			mediaId: $mediaId,
		);
		$media = new Media(
			id: $mediaId,
			userId: $command->userId,
			siteId: $command->siteId,
			title: 'image.png',
			accessibilityText: 'Image for testing',
			type: MediaType::Image,
			fileDetails: [],
		);

		$this->http->expects($this->once())
			->method('sendRequest')
			->with($this->httpMessageEqualTo(
				HttpMessageFactory::request(
					verb: HttpVerb::GET,
					url: HttpMessageFactory::uri('https://cdn.smol.blog/site/image.png'),
				),
			))
			->willReturn(new Response(body: ExampleFiles::artemisTwoEarthsetPicture()->getStream()));
		$this->fileRepo
			->method('saveFile')
			->willReturn([]);
		$this->perms->method('canUploadMedia')->willReturn(true);

		$this->expectEvent(new MediaCreated(
			entityId: $mediaId,
			aggregateId: $command->siteId,
			userId: $command->userId,
			title: $media->title,
			accessibilityText: $command->accessibilityText,
			mediaType: $media->type,
			fileDetails: [],
		));

		$this->app->execute($command);
	}

	public function testItRequiresAltText() {
		$this->expectException(InvalidValueProperties::class);

		new SideloadMedia(
			url: HttpMessageFactory::uri('https://smol.blog/test.png'),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: '',
		);
	}

	public function testItRequiresANonemptyTitleIfGiven() {
		$this->expectException(InvalidValueProperties::class);

		new SideloadMedia(
			url: HttpMessageFactory::uri('https://smol.blog/test.png'),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'alt text',
			title: '',
		);
	}

	public function testItFailsIfTheRequestIsNotSuccessful() {
		$this->expectException(InvalidValueProperties::class);

		$command = new SideloadMedia(
			url: HttpMessageFactory::uri('https://smol.blog/test.png'),
			userId: $this->randomId(),
			siteId: $this->randomId(),
			accessibilityText: 'alt text',
		);

		$this->perms->method('canUploadMedia')->willReturn(true);
		$this->http->method('sendRequest')->willReturn(HttpMessageFactory::response(code: 451));

		$this->app->execute($command);
	}
}
