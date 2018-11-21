<?php
/**
 * Destiny 2 Character Race Enum Class
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
 * Class CharacterRace
 * @package API\Library\Enum
 * @see https://bungie-net.github.io/multi/schema_Destiny-DestinyRace.html
 */
class CharacterRace extends Enum
{
	const Human = 0;
	const Awoken = 1;
	const Exo = 2;
	const Unknown = 3;
}