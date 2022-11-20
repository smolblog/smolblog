<?php

namespace Smolblog\Framework;

/**
 * An object that tells the Domain Model to do a thing.
 *
 * A Command is an object that represents a task and contains the minimum data needed for that task. A service
 * capable of performing the task should take the Command as its sole required parameter.
 *
 * This is analagous to sending commands to a REST API, except using native PHP objects. It is the preferred
 * method of talking to the Domain Model.
 */
abstract class Command extends Value {
}
