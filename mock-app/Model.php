<?php

namespace Smolblog\Mock;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Blueprint;
use PDO;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Core\Connector\Services\AuthRequestStateRepo;
use Smolblog\Core\Content\Types\Reblog\ExternalContentService;
use Smolblog\Framework\Infrastructure\QueryMemoizationService;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;

class Model extends DomainModel {
	public static function getDependencyMap(): array {
		return [
			Connector::class => [],
			QueryMemoizationService::class => MockMemoService::class,
			MockMemoService::class => [],
			SecurityService::class => [],
			AuthRequestStateRepo::class => Transients::class,
			Transients::class => ['db' => ConnectionInterface::class],
			ConnectionInterface::class => fn() => self::makeDatabase(),
			PermalinkService::class => ['bus' => MessageBus::class],
			MessageBus::class => MockMessageBus::class,
			MockMessageBus::class => [
				'provider' => ListenerProviderInterface::class,
				'log' => LoggerInterface::class,
			],
			MediaHandler::class => [],
			EmbedService::class => [],
			ExternalContentService::class => EmbedService::class,
		];
	}

	private static function makeDatabase(): ConnectionInterface {
		$capsule = new Manager();
		$capsule->addConnection([
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		]);
		$connection = $capsule->getConnection();
		$schema = $connection->getSchemaBuilder();

		$schema->create('channels', function(Blueprint $table) {
			$table->uuid('channel_uuid')->primary();
			$table->uuid('connection_uuid');
			$table->string('channel_key');
			$table->string('display_name');
			$table->jsonb('details');
		});
		$schema->create('channel_site_links', function(Blueprint $table) {
			$table->uuid('link_uuid')->primary();
			$table->uuid('channel_uuid');
			$table->uuid('site_uuid');
			$table->boolean('can_push');
			$table->boolean('can_pull');
		});
		$schema->create('connections', function(Blueprint $table) {
			$table->uuid('connection_uuid')->primary();
			$table->uuid('user_uuid');
			$table->string('provider');
			$table->string('provider_key');
			$table->string('display_name');
			$table->jsonb('details');
		});
		$schema->create('connector_events', function(Blueprint $table) {
			$table->uuid('event_uuid')->primary();
			$table->dateTimeTz('event_time');
			$table->uuid('connection_uuid');
			$table->uuid('user_uuid');
			$table->string('event_type');
			$table->text('payload');
		});
		$schema->create('content_events', function(Blueprint $table) {
			$table->uuid('event_uuid')->primary();
			$table->dateTimeTz('event_time');
			$table->uuid('content_uuid');
			$table->uuid('user_uuid');
			$table->uuid('site_uuid');
			$table->string('event_type');
			$table->text('payload');
		});
		$schema->create('standard_content', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->string('type');
			$table->string('title')->nullable();
			$table->text('body')->nullable();
			$table->uuid('author_uuid');
			$table->uuid('site_uuid');
			$table->string('permalink')->nullable();
			$table->dateTimeTz('publish_timestamp')->nullable();
			$table->string('visibility');
			$table->text('extensions');
		});
		$schema->create('content_syndication', function(Blueprint $table) {
			$table->uuid('row_uuid')->primary();
			$table->uuid('content_uuid');
			$table->uuid('channel_uuid')->nullable();
			$table->string('url')->nullable();
		});
		$schema->create('media', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->uuid('user_uuid');
			$table->uuid('site_uuid');
			$table->string('title');
			$table->string('accessibility_text');
			$table->string('type');
			$table->string('thumbnail_url');
			$table->string('default_url');
			$table->jsonb('file');
			$table->string('uploaded_at');
		});
		$schema->create('notes', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->text('markdown');
			$table->text('html');
		});
		$schema->create('pictures', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->text('media');
			$table->text('caption')->nullable();
			$table->string('media_html')->nullable();
			$table->text('caption_html')->nullable();
		});
		$schema->create('reblogs', function(Blueprint $table) {
			$table->uuid('content_uuid')->primary();
			$table->string('url');
			$table->text('comment')->nullable();
			$table->text('comment_html')->nullable();
			$table->text('url_info')->nullable();
		});
		$schema->create('followers', function(Blueprint $table) {
			$table->uuid('follower_uuid')->primary();
			$table->uuid('site_uuid');
			$table->string('provider');
			$table->string('provider_key');
			$table->string('display_name');
			$table->text('details');
		});
		$schema->create('site_events', function(Blueprint $table) {
			$table->uuid('event_uuid')->primary();
			$table->dateTimeTz('event_time');
			$table->uuid('site_uuid');
			$table->uuid('user_uuid');
			$table->string('event_type');
			$table->text('payload');
		});
		$schema->create('temp_options', function(Blueprint $table) {
			$table->increments('id');
			$table->text('key');
			$table->text('value');
			$table->text('expires');
		});

		return $connection;
	}
}
