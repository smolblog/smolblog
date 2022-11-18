<?php

namespace Smolblog\App\Endpoints;

use PHPUnit\Framework\TestCase;
use Smolblog\App\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Core\Connector\Channel;
use Smolblog\Core\Connector\ChannelReader;
use Smolblog\Core\Connector\Connection;
use Smolblog\Core\Connector\ConnectionReader;
use Smolblog\Test\EndpointTestToolkit;

final class UserConnectionsTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$conn1 = new Connection(userId: 5, provider: 'twitter', providerKey: '1566843', displayName: '@smolbirb', details: []);
		$conn2 = new Connection(userId: 5, provider: 'tumblr', providerKey: '1566843', displayName: 'birb@smol.blog', details: []);
		$connections = [$conn1, $conn2];

		$channels = [
			$conn1->id => [
				new Channel(connectionId: $conn1->id, channelKey: 'x', displayName: '@smolbirb', details: []),
			],
			$conn2->id => [
				new Channel(connectionId: $conn2->id, channelKey: '88794', displayName: 'smolbirbs', details: []),
				new Channel(connectionId: $conn2->id, channelKey: '88795', displayName: 'effyeahsmols', details: []),
				new Channel(connectionId: $conn2->id, channelKey: '88796', displayName: 'notsmol', details: []),
			],
		];

		$connectionRepo = $this->createStub(ConnectionReader::class);
		$connectionRepo->method('getConnectionsForUser')->willReturn($connections);

		$channelRepo = $this->createStub(ChannelReader::class);
		$channelRepo->method('getChannelsForConnections')->willReturn($channels);

		$this->endpoint = new UserConnections(connectionRepo: $connectionRepo, channelRepo: $channelRepo);
	}

	public function testItSendsBadRequestWhenUnauthenticated(): void {
		$response = $this->endpoint->run(new EndpointRequest());
		$this->assertEquals(400, $response->statusCode);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest(userId: 5);
		$response = $this->endpoint->run($request);

		$cid1 = Connection::buildId(provider: 'twitter', providerKey: '1566843');
		$cid2 = Connection::buildId(provider: 'tumblr', providerKey: '1566843');

		$expected = [
			[
				'id' => $cid1,
				'provider' => 'twitter',
				'displayName' => '@smolbirb',
				'channels' => [
					['id' => Channel::buildId(connectionId: $cid1, channelKey: 'x'), 'displayName' => '@smolbirb'],
				],
			], [
				'id' => $cid2,
				'provider' => 'tumblr',
				'displayName' => 'birb@smol.blog',
				'channels' => [
					['id' => Channel::buildId(connectionId: $cid2, channelKey: '88794'), 'displayName' => 'smolbirbs'],
					['id' => Channel::buildId(connectionId: $cid2, channelKey: '88795'), 'displayName' => 'effyeahsmols'],
					['id' => Channel::buildId(connectionId: $cid2, channelKey: '88796'), 'displayName' => 'notsmol'],
				]
			]
		];

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
		$this->assertEquals(['connections' => $expected], $response->body);
	}
}
