<?php
/**
 * Destiny 2 Component Enum Class
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
 * Class Component
 * @package API\Library\Enum
 * @see https://bungie-net.github.io/multi/schema_Destiny-DestinyComponentType.html
 */
class Component extends Enum
{
	const None = 0;
	const Profiles = 100;
	const VendorReceipts = 101;
	const ProfileInventories = 102;
	const ProfileCurrencies = 103;
	const Characters = 200;
	const CharacterInventories = 201;
	const CharacterProgressions = 202;
	const CharacterRenderData = 203;
	const CharacterActivities = 204;
	const CharacterEquipment = 205;
	const ItemInstances = 300;
	const ItemObjectives = 301;
	const ItemPerks = 302;
	const ItemRenderData = 303;
	const ItemStats = 304;
	const ItemSockets = 305;
	const ItemTalentGrids = 306;
	const ItemCommonData = 307;
	const ItemPlugStates = 308;
	const Vendors = 400;
	const VendorCategories = 401;
	const VendorSales = 402;
	const Kiosks = 500;
	const CurrencyLookups = 600;
}