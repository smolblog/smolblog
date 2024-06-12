<?php

use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Messaging\{Listener, Projection};
use Smolblog\Foundation\Value\Fields\{DateIdentifier, Identifier, NamedIdentifier, RandomIdentifier};
use Smolblog\Foundation\Value\Http\{HttpRequest, HttpResponse, HttpVerb};
use Smolblog\Foundation\Value\Messages\{Command, Query};
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

class_alias(DomainModel::class, 'Smolblog\Framework\Objects\DomainModel');

class_alias(Projection::class, 'Smolblog\Framework\Messages\Projection');
// class_alias(Command::class, 'Smolblog\Framework\Messages\Command');
// class_alias(Query::class, 'Smolblog\Framework\Messages\Query');

class_alias(HttpRequest::class, 'Smolblog\Framework\Objects\HttpRequest');
class_alias(HttpResponse::class, 'Smolblog\Framework\Objects\HttpResponse');
class_alias(HttpVerb::class, 'Smolblog\Framework\Objects\HttpVerb');

class_alias(AuthorizableMessage::class, 'Smolblog\Framework\Messages\AuthorizableMessage');
