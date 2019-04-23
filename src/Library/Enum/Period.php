<?php
/**
 * Destiny 2 Period Enum Class
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
 * Class Period
 * @package API\Library\Enum
 * @see https://bungie-net.github.io/multi/schema_Destiny-HistoricalStats-Definitions-PeriodType.html
 */
class Period extends Enum
{
	const None = 0;
	const Daily = 1;
	const AllTime = 2;
	const Activity = 3;
}