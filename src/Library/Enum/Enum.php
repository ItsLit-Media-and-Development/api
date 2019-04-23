<?php
/**
 * Base Enum Class
 *
 *
 * @package       API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright     Copyright (c) 2018 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 1.1
 * @filesource
 */

namespace API\Library\Enum;


abstract class Enum
{
	public static function getEnumStrings()
	{
		return array_keys(self::getEnums());
	}

	public static function getEnums()
	{
		$class = new \ReflectionClass(get_called_class());

		return $class->getConstants();
	}

	public static function getEnumStringFor($constant)
	{
		$flipped = array_flip(self::getEnums());

		return $flipped[$constant];
	}

	public static function getEnumStringsFor($constants)
	{
		if(!is_array($constants)) {
			$constants = [$constants];
		}

		$flipped = array_flip(self::getEnums());

		$mapped = array_map(function ($value) use ($flipped) {
			return $flipped[$value];
		}, $constants);

		return array_filter($mapped);
	}

	public static function hasEnum($enum)
	{
		return in_array($enum, self::getEnums());
	}
}