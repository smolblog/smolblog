<?php

namespace Smolblog\CoreDataSql;

use Doctrine\DBAL\Schema\Schema;

/**
 * Indicates a service that is responsible for a database table.
 */
interface DatabaseTableHandler {
	/**
	 * Add the table to the database schema.
	 *
	 * This allows the overall application to update the actual database schema to match the requirements needed by
	 * the individual handler.
	 *
	 * @param Schema $schema Schema builder object.
	 * @return Schema
	 */
	public static function addTableToSchema(Schema $schema): Schema;
}
