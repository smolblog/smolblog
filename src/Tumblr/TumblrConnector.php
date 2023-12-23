<?php

namespace Smolblog\Tumblr;

use DateTimeInterface;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorConfiguration;
use Smolblog\Core\Connector\ConnectorInitData;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\NoRefreshKit;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Tumblr\API\Client as TumblrClient;
use Tumblr\API\RequestException;

/**
 * Connect to Tumblr.
 */
class TumblrConnector implements Connector {
	use NoRefreshKit;

	/**
	 * Get the configuration for this connector.
	 *
	 * @return ConnectorConfiguration
	 */
	public static function getConfiguration(): ConnectorConfiguration {
		return new ConnectorConfiguration(
			key: 'tumblr',
		);
	}

	/**
	 * Create the service.
	 *
	 * @param TumblrClientFactory $factory Generate Tumblr clients.
	 */
	public function __construct(private TumblrClientFactory $factory) {
	}

	/**
	 * Get the information needed to start an OAuth session with the provider
	 *
	 * @param string $callbackUrl URL of the callback endpoint.
	 * @return ConnectorInitData
	 */
	public function getInitializationData(string $callbackUrl): ConnectorInitData {
		$handler = $this->factory->getAppClient()->getRequestHandler();
		$handler->setBaseUrl('https://www.tumblr.com/');

		$resp = $handler->request('POST', 'oauth/request_token', ['oauth_callback' => $callbackUrl]);
		$data = [];
		parse_str($resp->body, $data);

		return new ConnectorInitData(
			url: 'https://www.tumblr.com/oauth/authorize?oauth_token=' . $data['oauth_token'],
			state: $data['oauth_token'],
			info: ['secret' => $data['oauth_token_secret']],
		);
	}

	/**
	 * Handle the OAuth callback from the provider and create the credential
	 *
	 * @param string           $code Code given to the OAuth callback.
	 * @param AuthRequestState $info Info from the original request.
	 * @return null|Connection Created credential, null on failure
	 */
	public function createConnection(string $code, AuthRequestState $info): ?Connection {
		$handler = $this->factory->getUserClient(key: $info->key, secret: $info->info['secret'])->getRequestHandler();
		$handler->setBaseUrl('https://www.tumblr.com/');

		$accessResponse = $handler->request('POST', 'oauth/access_token', ['oauth_verifier' => $code]);

		$accessInfo = [];
		parse_str($accessResponse->body, $accessInfo);

		$client = $this->factory->getUserClient(
			key: $accessInfo['oauth_token'],
			secret: $accessInfo['oauth_token_secret']
		);
		$user = $client->getUserInfo()->user;

		return new Connection(
			userId: $info->userId,
			provider: self::getSlug(),
			providerKey: $this->findPrimaryBlogId($user->blogs),
			displayName: $user->name,
			details: [
				'key' => $accessInfo['oauth_token'],
				'secret' => $accessInfo['oauth_token_secret']
			],
		);
	}

	/**
	 * Get the channels enabled by the Connection.
	 *
	 * @param Connection $connection Account to get Channels for.
	 * @return Channel[] Array of Channels this Connection can use
	 */
	public function getChannels(Connection $connection): array {
		$client = $this->factory->getUserClient(...$connection->details);
		$user = $client->getUserInfo()->user;

		return array_map(
			fn($blog) => new Channel(
				connectionId: $connection->id,
				channelKey: $blog->uuid,
				displayName: "$blog->name",
				details: [
					'url' => $blog->url,
				]
			),
			$user->blogs
		);
	}

	/**
	 * Push a new post to a blog.
	 *
	 * @param Content    $content        Content to push.
	 * @param Channel    $toChannel      Blog to push to.
	 * @param Connection $withConnection Connection to use.
	 * @return void
	 */
	public function push(Content $content, Channel $toChannel, Connection $withConnection): void {
		$client = $this->factory->getUserClient(...$withConnection->details);
		$payload = [
			'state' => 'published',
			'tags' => array_map(
				fn($tag) => $tag->text,
				$content->extensions[Tags::class]?->tags ?? []
			),
			'date' => $content->publishTimestamp->format(DateTimeInterface::RFC3339_EXTENDED),
			'native_inline_images' => true,
			'format' => 'markdown',
		];

		$tumblrPostInfo =
			$content->type->getTypeKey() === 'reblog' ?
			$this->getPostInfo($content->type->url, $client) :
			null;
		if (isset($tumblrPostInfo)) {
			$payload['comment'] = $content->type->comment;
			$payload['id'] = $tumblrPostInfo['id'];
			$payload['reblog_key'] = $tumblrPostInfo['key'];

			$client->postRequest("v2/blog/$toChannel->channelKey/post/reblog", $payload, false);
			return;
		}

		switch ($content->type->getTypeKey()) {
			case 'note':
				$payload['type'] = 'text';
				$payload['body'] = $content->type->text;
				break;

			case 'picture':
				$payload['type'] = 'photo';
				$payload['link'] = $content->permalink;
				$payload['source'] = $content->type->media[0]->defaultUrl;
				$payload['caption']  = $content->type->caption;
				break;

			case 'reblog':
				$payload['type'] = 'video';
				$payload['caption'] = $content->type->comment;
				$payload['embed'] = $content->type->info?->embed ?? $content->type->url;
		}

		$client->postRequest("v2/blog/$toChannel->channelKey/post", $payload, false);
	}

	/**
	 * Get the primary blog from the user's list of blogs.
	 *
	 * @param array $blogs User's blogs.
	 * @return string Primary blog UUID
	 */
	private function findPrimaryBlogId(array $blogs): string {
		foreach ($blogs as $blog) {
			if ($blog->primary) {
				return $blog->uuid;
			}
		}
		return $blogs[0]->uuid;
	}

	/**
	 * Get information about the given tumblr URL if it's a tumblr url.
	 *
	 * @param string       $url    URL to investigate.
	 * @param TumblrClient $client Tumblr client to use.
	 * @return array|null
	 */
	private function getPostInfo(string $url, TumblrClient $client): ?array {
		// Method from <https://milandinic.com/2015/07/01/post-id-tumblr-url-php/>.
		$parsed = parse_url($url);
		$pathParts = explode('/', trim($parsed['path'], '/'));
		$postId = $pathParts[1] ?? null;

		// No post ID from the URL; this isn't Tumblr.
		if (!isset($postId) || !is_numeric($postId)) {
			return null;
		}

		$blogName = match ($parsed['host']) {
			'www.tumblr.com' => $pathParts[0],
			default => $parsed['host'],
		};
		try {
			$data = $client->getBlogPosts($blogName, ['id' => $postId]);

			if (empty($data->posts)) {
				// No data, probably not a Tumblr url.
				return null;
			}

			return [
				'id' => $data->posts[0]->id,
				'key' => $data->posts[0]->reblog_key,
			];
		} catch (RequestException) {
			// Bad request, probably not a Tumblr url.
			return null;
		}
	}
}
