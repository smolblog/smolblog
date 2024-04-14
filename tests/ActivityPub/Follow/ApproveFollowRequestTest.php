<?php

namespace Smolblog\ActivityPub\Follow;

use Smolblog\Core\Site\Site;
use Smolblog\Framework\ActivityPub\Objects\Actor;
use Smolblog\Framework\ActivityPub\Objects\ActorPublicKey;
use Smolblog\Framework\ActivityPub\Objects\ActorType;
use Smolblog\Framework\ActivityPub\Objects\Follow;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

final class ApproveFollowRequestTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$siteId = $this->randomId(true);
		$followingActorId = 'https://smol.blog/'.$this->randomId().'/activitypub/actor';
		$followedActorId = "https://smol.blog/$siteId/activitypub/actor";

		$this->subject = new ApproveFollowRequest(
			userId: $this->randomId(true),
			request: new Follow(
				id: 'https://smol.blog/outbox/' . $this->randomId(),
				actor: $followingActorId,
				object: $followedActorId,
			),
			actor: new Actor(
				id: $followingActorId,
				type: ActorType::Person,
				inbox: $followedActorId . '/inbox',
			),
			site: new Site(
				id: $siteId,
				handle: 'tester',
				displayName: 'TestTesterson',
				baseUrl: 'https://test.smol.blog/',
				publicKey: 'PUBLIC_KEY',
			),
		);
	}

	public function testItWillDeserializeAComplexObject() {
		$array = array (
			'userId' => '4cf81e87-02ae-492c-9458-eef01a968d45',
			'request' => array (
				'@context' => 'https://www.w3.org/ns/activitystreams',
				'type' => 'Follow',
				'id' => 'https://activitypub.academy/87fd9257-7b18-4817-8341-fd6bf5a86170',
				'actor' => 'https://activitypub.academy/users/beguca_dedashul',
				'object' => 'https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/actor',
			),
			'site' => array (
				'id' => '426a9e54-435f-4135-9252-0d0a6ddd1dba',
				'handle' => 'oddevan',
				'displayName' => 'Evan Hildreth',
				'baseUrl' => 'http://oddevan.smol.blog',
				'description' => NULL,
				'publicKey' => '-----BEGIN PUBLIC KEY----- MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyKBsUQR9J+JRQd7pFXO3 ATrJIYcEp309IWapF3Oe/aEklSenWMZVrLa14jub12WY6SYl981XN4PN0tqTTlSV aAZDUWDQd1XdGiPHKo37Yo8Qijc7QOu3tWM082ZXC0PKSlfsG/mLK5bPIY97BUE9 kk5J93RVTz7mj7Gw/bnEqB5xiUHxrsqQhblyowuRIj1jVr0iyU0aUzxmEaTDlZ6j fsaeEc3FpuvLEJP+fdCTh3gXSg+JvqQ/fHb+aYbRLqKuuJQ7l7+yxJvLdYuMHkp1 KBDgoOIeOxoSeFj3i3Bceb2U7QSkolY5RCOo3fTq+GhJXQua2KeL7B5kSefofYyQ wQIDAQAB -----END PUBLIC KEY-----',
			),
			'actor' => array (
				'@context' => array (
					'https://www.w3.org/ns/activitystreams',
					'https://w3id.org/security/v1',
				),
				'type' => 'Person',
				'id' => 'https://activitypub.academy/users/beguca_dedashul',
				'publicKey' => array (
					'id' => 'https://activitypub.academy/users/beguca_dedashul#main-key',
					'owner' => 'https://activitypub.academy/users/beguca_dedashul',
					'publicKeyPem' => '-----BEGIN PUBLIC KEY----- MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzOQXkYZN7LoATFNQ3mm/ SeBxRiI0BKpoRLSELCZR9U4GcZ2wHGTENvc++3h63vgIVXzgjWHSaMj1w+LvG3c4 JV4FrOFGzrxtQvyFDUyNmihRU2+cxqLQiKuZbUxrKFtyA6hdmiCi8IX41UZiA9QB hmXMP0REj/OSth0FS8+o8iMN4kB0Qvq9JSrIkV0Lwv3jJs/LP9QLjX5fgJUVTbdP pVus9AhLUJjZ3i/KIGehn9bbwg8PnEQOHuEO7lxO0YXetbv7+HQEV+jJAWY/5nJv FUTQTIOeGFa8FkdDgYwAxyXDzumrjY69DzXcXxkzro1spagh5wsRC08o3Cyi1mTm 6QIDAQAB -----END PUBLIC KEY----- ',
				),
				'following' => 'https://activitypub.academy/users/beguca_dedashul/following',
				'followers' => 'https://activitypub.academy/users/beguca_dedashul/followers',
				'inbox' => 'https://activitypub.academy/users/beguca_dedashul/inbox',
				'outbox' => 'https://activitypub.academy/users/beguca_dedashul/outbox',
				'featured' => 'https://activitypub.academy/users/beguca_dedashul/collections/featured',
				'featuredTags' => 'https://activitypub.academy/users/beguca_dedashul/collections/tags',
				'preferredUsername' => 'beguca_dedashul',
				'name' => 'Beguca Dedashul',
				'summary' => '',
				'url' => 'https://activitypub.academy/@beguca_dedashul',
				'manuallyApprovesFollowers' => '',
				'discoverable' => '',
				'published' => '2024-01-06T00:00:00Z',
				'devices' => 'https://activitypub.academy/users/beguca_dedashul/collections/devices',
				'tag' => array ( ),
				'attachment' => array ( ),
				'endpoints' => array (
					'sharedInbox' => 'https://activitypub.academy/inbox',
				),
			),
		);

		$object = new ApproveFollowRequest(
			userId: Identifier::fromString('4cf81e87-02ae-492c-9458-eef01a968d45'),
			request: new Follow(
				id: 'https://activitypub.academy/87fd9257-7b18-4817-8341-fd6bf5a86170',
				actor: 'https://activitypub.academy/users/beguca_dedashul',
				object: 'https://smol.blog/wp-json/smolblog/v2/site/426a9e54-435f-4135-9252-0d0a6ddd1dba/activitypub/actor',
			),
			site: new Site(
				id: Identifier::fromString('426a9e54-435f-4135-9252-0d0a6ddd1dba'),
				handle: 'oddevan',
				displayName: 'Evan Hildreth',
				baseUrl: 'http://oddevan.smol.blog',
				description: NULL,
				publicKey: '-----BEGIN PUBLIC KEY----- MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyKBsUQR9J+JRQd7pFXO3 ATrJIYcEp309IWapF3Oe/aEklSenWMZVrLa14jub12WY6SYl981XN4PN0tqTTlSV aAZDUWDQd1XdGiPHKo37Yo8Qijc7QOu3tWM082ZXC0PKSlfsG/mLK5bPIY97BUE9 kk5J93RVTz7mj7Gw/bnEqB5xiUHxrsqQhblyowuRIj1jVr0iyU0aUzxmEaTDlZ6j fsaeEc3FpuvLEJP+fdCTh3gXSg+JvqQ/fHb+aYbRLqKuuJQ7l7+yxJvLdYuMHkp1 KBDgoOIeOxoSeFj3i3Bceb2U7QSkolY5RCOo3fTq+GhJXQua2KeL7B5kSefofYyQ wQIDAQAB -----END PUBLIC KEY-----',
			),
			actor: new Actor(
				type: ActorType::Person,
				id: 'https://activitypub.academy/users/beguca_dedashul',
				publicKey: new ActorPublicKey(
					id: 'https://activitypub.academy/users/beguca_dedashul#main-key',
					owner: 'https://activitypub.academy/users/beguca_dedashul',
					publicKeyPem: '-----BEGIN PUBLIC KEY----- MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzOQXkYZN7LoATFNQ3mm/ SeBxRiI0BKpoRLSELCZR9U4GcZ2wHGTENvc++3h63vgIVXzgjWHSaMj1w+LvG3c4 JV4FrOFGzrxtQvyFDUyNmihRU2+cxqLQiKuZbUxrKFtyA6hdmiCi8IX41UZiA9QB hmXMP0REj/OSth0FS8+o8iMN4kB0Qvq9JSrIkV0Lwv3jJs/LP9QLjX5fgJUVTbdP pVus9AhLUJjZ3i/KIGehn9bbwg8PnEQOHuEO7lxO0YXetbv7+HQEV+jJAWY/5nJv FUTQTIOeGFa8FkdDgYwAxyXDzumrjY69DzXcXxkzro1spagh5wsRC08o3Cyi1mTm 6QIDAQAB -----END PUBLIC KEY----- ',
				),
				following: 'https://activitypub.academy/users/beguca_dedashul/following',
				followers: 'https://activitypub.academy/users/beguca_dedashul/followers',
				inbox: 'https://activitypub.academy/users/beguca_dedashul/inbox',
				outbox: 'https://activitypub.academy/users/beguca_dedashul/outbox',
				featured: 'https://activitypub.academy/users/beguca_dedashul/collections/featured',
				featuredTags: 'https://activitypub.academy/users/beguca_dedashul/collections/tags',
				preferredUsername: 'beguca_dedashul',
				name: 'Beguca Dedashul',
				summary: '',
				url: 'https://activitypub.academy/@beguca_dedashul',
				manuallyApprovesFollowers: '',
				discoverable: '',
				published: '2024-01-06T00:00:00Z',
				devices: 'https://activitypub.academy/users/beguca_dedashul/collections/devices',
				tag: [],
				attachment: [],
				endpoints: ['sharedInbox' => 'https://activitypub.academy/inbox'],
			),
		);

		$this->assertEquals($object, ApproveFollowRequest::deserializeValue($array));
		$this->assertEquals($array, $object->serializeValue());
		$this->assertEquals('https://activitypub.academy/users/beguca_dedashul/inbox', $object->actor->inbox);
	}
}
