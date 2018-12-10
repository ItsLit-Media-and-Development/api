<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 09/12/2018
 * Time: 09:03
 */

namespace API\Controllers;

use API\Library;

class Games extends Library\BaseController
{
	public function __construct()
	{
		parent::__construct();
	}

	public function eightball()
	{
		$user = $this->_params[0];

		$outcomes = [
			0 => "It is Certain",
			1 => "Outlook is good",
			2 => "You may rely on it",
			3 => "Ask again later",
			4 => "Concentrate and ask again",
			5 => "Reply hazy, try again",
			6 => "No",
			7 => "My sources say no",
			8 => "Yes"
		];

		return $this->_output->output(200, $outcomes[rand(0, 8)] . ", " . $user, true);
	}

	public function cointoss()
	{
		$user = $this->_params[0];
		$call = (strtolower($this->_params[1]) == "heads") ? 1 : ((strtolower($this->_params[1]) == "tails") ? 0 : -1);

		//lets check to make sure it is a valid call first
		if($call < 0) {
			$msg = "I have never seen a coin with a side called that $user";
		} else {
			//0 is tails, 1 is heads
			$coin = rand(0, 1);

			if($coin === $call) {
				//We have a winner!
				$msg = "You made the right call $user, the coin landed on " . (($coin == 0) ? "tails" : "heads");
			} else {
				$msg = "Ouch, bad call $user, the coin landed on " . (($coin == 0) ? "tails" : "heads");
			}
		}

		return $this->_output->output(200, $msg, true);
	}

	public function roulette()
	{

	}
}