<?php
/**
 * League of Legends Endpoint
 *
 * @package       API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright     Copyright (c) 2018 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 1.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;

class League extends Library\BaseController
{
    private $_riot;

    public function __construct()
    {
		parent::__construct();

        $this->_riot   = new Library\Riot();
    }

    /**
     * Returns a list of all champions currently live in LoL
     *
     * @return array
     */
    public function getChampions()
    {
        $this->_log->set_message("League::getChampions() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        //Set the platform
        $this->_riot->setPlatform($this->_params[0]);
		$bot = (isset($this->_params[1])) ? $this->_params[1] : false;

		$output = $this->_riot->get_champs(true);

        return $this->_output->output(200, $output, $bot);
    }

    /**
     * Returns a list of all free champions currently live in LoL
     *
     * @return array
     */
    public function getFreeChampions()
    {
        $this->_log->set_message("League::getFreeChampions() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        //Set the platform
        $this->_riot->setPlatform($this->_params[0]);
        $bot = (isset($this->_params[1])) ? $this->_params[1] : false;

		//$output = $this->_riot->get('platform/v3/champions?freeToPlay=true');
		$output = $this->_riot->get('platform/v3/champion-rotations');

		//could add the option to pull the user's level and add on the freeChampionIdsForNewPlayers option
		$output = $output['freeChampionIds'];

		for($i = 0; $i < count($output); $i++) {
			$output[$i] = $this->_riot->get_champ_name($output[$i]);
		}

        return $this->_output->output(200, $output, $bot);
    }

    /**
     * Returns all champions mastery for a player
     *
	 * @TODO work out what stupid time format Riot uses for the lastPlayTime result and convert it to datediff
	 *
     * @return array
     */
    public function getChampionMastery()
    {
		$this->_log->set_message("League::getChampionMastery() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		//$date = new \DateTime();

        //Set the platform and user
        $this->_riot->setPlatform($this->_params[0]);

        $id      = $this->_riot->get_user_id($this->_params[1]);
        $champId = (isset($this->_params[2])) ? $this->_params[2] : false;
        $bot     = (isset($this->_params[3])) ? $this->_params[3] : false;

        $output = $this->_riot->get(($champId !== false) ?
            'champion-mastery/v3/champion-masteries/by-summoner/' . $id . '/by-champion/' . $champId :
            'champion-mastery/v3/champion-masteries/by-summoner/' . $id);

		//lets add summoner name in for ease
		for($i = 0; $i < count($output); $i++) {
			$output[$i]['championName'] = $this->_riot->get_champ_name($output[$i]['championId']);
			//$date->setTimestamp($output[$i]['lastPlayTime']);
			//$output[$i]['playDateDiff'] = $date->format('U = d-m-Y H:i:s');
		}

        return $this->_output->output(200, $output, $bot);
    }

    /**
     * Gets information on a summoner's current game
     *
     * @return array
     */
    public function getCurrentGame()
    {
		$this->_log->set_message("League::getCurrentGame() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

		return $this->_output->output(501, "Function not implemented", false);

		/*$this->_log->set_message("League::getCurrentGame() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        //Set the platform and user
        $this->_riot->setPlatform($this->_params[0]);

        $id  = $this->_riot->get_user_id($this->_params[1]);
        $bot = (isset($this->_params[2])) ? $this->_params[2] : false;

        $output = $this->_riot->get('spectator/v3/active-games/by-summoner/' . $id);

        return $this->_output->output(200, $output, $bot);*/
    }

	/**
	 * Retrieves the summoner's league info
	 *
	 * @return array|string
	 */
    public function getLeague()
    {
        $this->_log->set_message("League::getCurrentGame() Called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        //Set the platform and user
        $this->_riot->setPlatform($this->_params[0]);

        $id  = $this->_riot->get_user_id($this->_params[1]);
        $bot = (isset($this->_params[2])) ? $this->_params[2] : false;

		$output = $this->_riot->get('league/v3/positions/by-summoner/' . $id);

        return $this->_output->output(200, $output, $bot);
    }

}