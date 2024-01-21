<?php

namespace Smolblog\Framework\Messages;

/**
 * An object that tells the Domain Model to do a thing.
 *
 * A Command is an object that represents a task and contains the minimum data needed for that task. A service's
 * function capable of performing the task should take the Command as its sole required parameter.
 *
 * This is analagous to sending commands to a REST API, except using native PHP objects. It is the preferred
 * method of talking to the Domain Model.
 *
 * Comamnds can provide a Query object that will return a truthy or falsy value in order to authorize the command.
 */
abstract class Command extends Message {
}
