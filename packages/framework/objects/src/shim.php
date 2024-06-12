<?php

use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Messaging\{Listener, MessageBus, Projection};
use Smolblog\Foundation\Value\Fields\{DateIdentifier, Identifier, NamedIdentifier, RandomIdentifier};
use Smolblog\Foundation\Value\Http\{HttpRequest, HttpResponse, HttpVerb};
use Smolblog\Foundation\Value\Messages\{Command, Query};
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

// class_alias(Command::class, 'Smolblog\Framework\Messages\Command');
// class_alias(Query::class, 'Smolblog\Framework\Messages\Query');
class_alias(MessageBus::class, 'Smolblog\Framework\Messages\MessageBus');
