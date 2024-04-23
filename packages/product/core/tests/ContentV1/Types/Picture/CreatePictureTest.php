<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\TestCase;

final class CreatePictureTest extends TestCase {
	public function testItRequiresAuthorPermissions() {
		$command = new CreatePicture(
			mediaIds: [$this->randomId()],
			userId: $this->randomId(),
			siteId: $this->randomId(),
		);

		$this->assertEquals(
			new UserHasPermissionForSite(
				siteId: $command->siteId,
				userId: $command->userId,
				mustBeAdmin: false,
				mustBeAuthor: true
			),
			$command->getAuthorizationQuery()
		);
	}

	public function testItIsCreatedWithADefaultContentId() {
		$command = new CreatePicture(
			mediaIds: [$this->randomId()],
			caption: 'A thing.',
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertInstanceOf(Identifier::class, $command->contentId);
	}

	public function testItCanBeGivenAContentId() {
		$id = $this->randomId();
		$command = new CreatePicture(
			mediaIds: [$this->randomId()],
			caption: 'A thing.',
			contentId: $id,
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals($id, $command->contentId);
	}

	public function testItWillSerializeCorrectly() {
		$command = new CreatePicture(
			contentId: Identifier::fromString('1dae7f8a-9a94-4fd6-9f76-89450fcbf3c5'),
			siteId: Identifier::fromString('fcaff50e-bb2b-4017-bda5-7a23e1ece9f6'),
			userId: Identifier::fromString('c1004550-221d-45ce-b4aa-711093cf9945'),
			mediaIds: [
				Identifier::fromString('75c171cc-447f-4703-8d3e-68dcc250aa1c'),
				Identifier::fromString('c27220f2-a47a-4510-962a-7cadd32424de'),
			],
			caption: 'The cliffs of insanity!',
		);
		$expected = json_encode([
			'contentId' => '1dae7f8a-9a94-4fd6-9f76-89450fcbf3c5',
			'siteId' => 'fcaff50e-bb2b-4017-bda5-7a23e1ece9f6',
			'userId' => 'c1004550-221d-45ce-b4aa-711093cf9945',
			'mediaIds' => [
				'75c171cc-447f-4703-8d3e-68dcc250aa1c',
				'c27220f2-a47a-4510-962a-7cadd32424de',
			],
			'caption' => 'The cliffs of insanity!',
		]);

		$this->assertJsonStringEqualsJsonString($expected, json_encode($command));
	}

	public function testItWillStripArrayKeys() {
		$command = new CreatePicture(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			mediaIds: [
				'photo' => Identifier::fromString('d2655068-427e-40e3-bbd4-83154da2d443'),
				'image' => Identifier::fromString('9fcfde4b-63f6-41a1-a521-d2bcd7e50ffb'),
			]
		);

		$this->assertEquals([
			Identifier::fromString('d2655068-427e-40e3-bbd4-83154da2d443'),
			Identifier::fromString('9fcfde4b-63f6-41a1-a521-d2bcd7e50ffb'),
		], $command->mediaIds);
	}
}
