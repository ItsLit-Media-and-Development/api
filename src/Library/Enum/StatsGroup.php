<?php
/**
 * Destiny 2 Stats Group Enum Class
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
 * Class StatsGroup
 * @package API\Library\Enum
 * @see https://bungie-net.github.io/multi/schema_Destiny-HistoricalStats-Definitions-DestinyStatsGroupType.html
 */
class StatsGroup extends Enum
{
	const None = 0;
	const General = 1;
	const Weapons = 2;
	const Medals = 3;
	const ReservedGroups = 100;
	const Leaderboard = 101;
	const Activity = 102;
	const UniqueWeapon = 103;
	const Internal = 104;
}