<?php
/**
 * Destiny 2 Character Gender Enum Class
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
 * Class CharacterGender
 * @package API\Library\Enum
 * @see https://bungie-net.github.io/multi/schema_Destiny-DestinyGender.html
 */
class CharacterGender extends Enum
{
	const Male = 0;
	const Female = 1;
	const Unknown = 2;
}