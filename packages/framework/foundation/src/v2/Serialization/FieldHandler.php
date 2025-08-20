<?php

namespace Smolblog\Foundation\v2\Serialization;

use Crell\Serde\Attributes\Field as FieldAttribute;
use Crell\Serde\DeformatterResult;
use Crell\Serde\Deserializer;
use Crell\Serde\PropertyHandler\{Exporter, Importer};
use Crell\Serde\Serializer;
use Smolblog\Foundation\v2\Value\Traits\Field;

class FieldHandler implements Exporter, Importer {
	public function canExport(FieldAttribute $field, mixed $value, string $format): bool {
		return $value instanceof Field;
	}

	public function exportValue(Serializer $serializer, FieldAttribute $field, mixed $value, mixed $runningValue): mixed {
		// @var Field
		$fieldObject = $value;
		return $serializer->formatter->serializeString($runningValue, $field, $fieldObject->toString());
	}

	public function canImport(FieldAttribute $field, string $format): bool {
		return is_a($field->phpType, Field::class, allow_string: true);
	}

	/**
	 * Undocumented function
	 *
	 * @param Deserializer   $deserializer
	 * @param FieldAttribute $field
	 * @param mixed          $source
	 * @return mixed
	 */
	public function importValue(Deserializer $deserializer, FieldAttribute $field, mixed $source): mixed {
		$string = $deserializer->deformatter->deserializeString($source, $field);

		if ($string instanceof DeformatterResult || $string === null) {
			return null;
		}

		// @var class-string<Field>
		$fieldType = $field->phpType;
		return $fieldType::fromString($string);
	}
}
