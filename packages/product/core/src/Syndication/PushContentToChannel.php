<?php

namespace Smolblog\Core\Syndication;

use Smolblog\Core\Connector\Entities\Channel;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Queries\SiteHasPermissionForChannel;
use Smolblog\Core\ContentV1\Content;
use Smolblog\Core\ContentV1\ContentVisibility;
use Smolblog\Core\ContentV1\Extensions\Syndication\Syndication;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;

/**
 * Push the given content to the given channel using the given connection. Full objects as this is intended to be an
 * asynchronous operation.
 */
readonly class PushContentToChannel extends Command implements AuthorizableMessage {
	/**
	 * Construct the command.
	 *
	 * @throws InvalidCommandParametersException When the parameters do not align.
	 *
	 * @param Content    $content    Content to syndicate.
	 * @param Channel    $channel    Channel to syndicate to.
	 * @param Connection $connection Connection to authorize the syndication.
	 */
	public function __construct(
		public readonly Content $content,
		public readonly Channel $channel,
		public readonly Connection $connection,
	) {
		if ($content->visibility !== ContentVisibility::Published) {
			throw new InvalidCommandParametersException(
				command: $this,
				message: 'Only published content can be pushed to channels.'
			);
		}
		if (strval($channel->connectionId) !== strval($connection->id)) {
			throw new InvalidCommandParametersException(
				command: $this,
				message: 'Channel and connection do not match.'
			);
		}
		if (
			!in_array(
				strval($channel->id),
				array_map(
					fn($val) => strval($val),
					$content->extensions[Syndication::class]?->channels ?? []
				)
			)
		) {
			throw new InvalidCommandParametersException(
				command: $this,
				message: 'Content syndication does not include this channel.'
			);
		}
	}

	/**
	 * Ensure the site has push permissions for the channel.
	 *
	 * @return SiteHasPermissionForChannel
	 */
	public function getAuthorizationQuery(): Query {
		return new SiteHasPermissionForChannel(
			siteId: $this->content->siteId,
			channelId: $this->channel->id,
			mustPush: true,
		);
	}

	/**
	 * Serialize the object.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return [
			'content' => $this->content->toArray(),
			'channel' => $this->channel->toArray(),
			'connection' => $this->connection->toArray(),
		];
	}

	/**
	 * Deserialize the object.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		return new PushContentToChannel(
			content: Content::fromArray($data['content']),
			channel: Channel::fromArray($data['channel']),
			connection: Connection::fromArray($data['connection']),
		);
	}
}
