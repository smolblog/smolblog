<?php

use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\Messaging\{Listener, Projection};
use Smolblog\Foundation\Value\Fields\{DateIdentifier, Identifier, NamedIdentifier, RandomIdentifier};
use Smolblog\Foundation\Value\Http\{HttpRequest, HttpResponse, HttpVerb};
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

class_alias(DomainModel::class, 'Smolblog\Framework\Objects\DomainModel');

class_alias(Listener::class, 'Smolblog\Framework\Messages\Listener');
class_alias(Projection::class, 'Smolblog\Framework\Messages\Projection');

class_alias(Identifier::class, 'Smolblog\Framework\Objects\Identifier');
class_alias(DateIdentifier::class, 'Smolblog\Framework\Objects\DateIdentifier');
class_alias(RandomIdentifier::class, 'Smolblog\Framework\Objects\RandomIdentifier');
class_alias(NamedIdentifier::class, 'Smolblog\Framework\Objects\NamedIdentifier');

class_alias(HttpRequest::class, 'Smolblog\Framework\Objects\HttpRequest');
class_alias(HttpResponse::class, 'Smolblog\Framework\Objects\HttpResponse');
class_alias(HttpVerb::class, 'Smolblog\Framework\Objects\HttpVerb');

class_alias(AuthorizableMessage::class, 'Smolblog\Framework\Messages\AuthorizableMessage');
