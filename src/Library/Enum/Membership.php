<?php
/**
 * Destiny 2 Membership Enum Class
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
 * Class Membership
 * @package API\Library\Enum
 * @see https://bungie-net.github.io/multi/schema_BungieMembershipType.html
 */
class Membership extends Enum
{
	const None = 0;
	const TigerXbox = 1;
	const TigerPsn = 2;
	const TigerBlizzard = 4;
	const TigerDemon = 10;
	const BungieNext = 254;
	const All = -1;
}