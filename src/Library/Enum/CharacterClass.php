<?php
/**
 * Destiny 2 Character Enum Class
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

/**
 * Class CharacterClass
 * @package API\Library\Enum
 * @see https://bungie-net.github.io/multi/schema_Destiny-DestinyGender.html
 */
class CharacterClass extends Enum
{
	const Titan = 0;
	const Hunter = 1;
	const Warlock = 2;
	const Unknown = 3;
}