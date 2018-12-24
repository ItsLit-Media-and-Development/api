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
		$this->_log->set_message("Games::eightball() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$user = $this->_params[0];

		$outcomes = [
			0  => "It is Certain",
			1  => "Outlook is good",
			2  => "You may rely on it",
			3  => "Ask again later",
			4  => "Concentrate and ask again",
			5  => "Reply hazy, try again",
			6  => "No",
			7  => "My sources say no",
			8  => "Yes",
			9  => "What is that you’re wearing!? Oh wait, did you ask a question. Sorry distracted by that hideo- um, unique choice ya got there.... ok, ask again",
			10 => "Oh? You wanted me to answer that...as in it was a real question... whoops, sorry",
			11 => "Yes yes, the outcome looks good. All positive vibes & such. Now, can I finish watching my show",
			12 => "Sooooo, let me get this straight....out of anything you could ask me, you asked that? Ok... mmmm...oh dear! Yeah, not what you wanted. Sorry",
			13 => "Word of advice, check your milk date before you drink it... actually never mind, that was pretty funny to watch... :laughing: Oh & yeah yeah, sure to your question",
			14 => "Reply hazy... cause I’m not awake yet. Ask again later",
			15 => "I said ask later, this isn’t later...",
			16 => "Ok, not bad! Out look is pretty good",
			17 => "Oh yeah, hope you weren’t banking on me saying something positive",
			18 => "Yes, go for it! But don’t blame me if it goes bad",
			19 => "Yes Yes....now, ask me the real question you have sitting there in your mind",
			20 => "*tap tap* Is this thing on? No, not a mic....your brain! Hellllooo Mcfly, switch on",
			21 => "Well lets see....it could be good, it could be bad. Which do you think it will be",
			22 => "Not in this life time",
			23 => "Here is a quarter, call someone who half cares",
			24 => "Seek the answer within... And leave me alone",
			25 => "I encourage you to go for it",
			26 => "Don't know, blame it on the rain",
			27 => "This question... *again*, really",
			28 => "Why don’t you do us all a favor...put the phone down (or better yet the controller) & go to sleep",
			29 => "I’m in a good mood, and yes the outcome looks good for you! No sarcasm....this time",
			30 => "Why don't you ask the last idiot who asked a question to ask this one for you",
			31 => "Reply hazy... wait... It is clearing... OWWW my eyes, thanks",
			32 => "Hahahahahaha you really want me to answer that",
			33 => "Yes, yes, 1000 times yes",
			34 => "You told me to F off before, why should I answer you",
			35 => "Deep breath, concentrate and ask agai... actually the answer will still be no",
			36 => "And the answer is YES! Quick, ask me another one before this wears off",
			37 => "Hahahhaa sorry, I just saw you trying that. All I have to say is please record it",
			38 => "Outlook says no.... Hey, don’t get mad at me, you asked the question",
			39 => "Outlook is good, if you know where to look, how far to look, and if you look enough",
			40 => "Quit asking such mean things...You wouldn’t like it if your friends picked on you like that. Oh wait, never mind....they do",
			41 => "Yes,  I did answer the question, you just weren’t paying attention & no I’m not repeating myself",
			42 => "Sleep, what is this sleep everyone speaks of? I wanna try it"
		];

		return $this->_output->output(200, $outcomes[rand(0, sizeof($outcomes))] . ", " . $user, true);
	}

	public function cointoss()
	{
		$this->_log->set_message("Games::cointoss() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

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
		$this->_log->set_message("Games::roulette() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$user = $this->_params[0];

		$outcomes = [
			0 => "*click* it was empty, you were lucky",
			1 => "**boom** looks like you have caused a mess",
			2 => "The gun fired but $user's skull was so thick the bullet couldn't penetrate",
			3 => "$user is notihng more then a blood splatter on the wall",
			4 => "The gun fired and $user dropped to the floor like a sack of spuds",
			5 => "**BOOM** the gun fired and $user somehow missed at point blank range!",
			6 => "The gun goes off and misses but Sig_Shezza stabs you in the back instead",
		];

		return $this->_output->output(200, $outcomes[rand(0, sizeof($outcomes))], true);
	}

	/**
	 * results 0 = loss, -1 = tie, 1 = win
	 */
	public function rps()
	{
		$this->_log->set_message("Games::roulette() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$call = strtolower($this->_params[0]);
		$user = $this->_params[1];
		$result = "0";

		$allowed = ['paper', 'rock', 'scissors'];

		if(!in_array($call, $allowed)) {
			$output = "I have never heard of $call, the game is **Rock, Paper, Scissors**, you must choose any **one** of them";
		} else {
			$botCall = rand(0, 2);

			switch($botCall) {
				//Rock
				case 0:
					$result = ($call == "paper") ? "1" : (($call == "rock") ? "-1" : "0");

					break;
				case 1:
					//paper
					$result = ($call == "scissors") ? "1" : (($call == "paper") ? "-1" : "0");

					break;
				case 2:
					//scissors
					$result = ($call == "rock") ? "1" : (($call == "scissors") ? "-1" : "0");

					break;
			}

			$output = "$user chose " . ucwords($call) . ", I chose ";
			$output .= ($botCall == 0) ? "Rock!" : (($botCall == 1) ? "Paper!" : "Scissors!");
			$output .= ($result == "1") ? " You win :(" : ($result == "-1" ? " We drew!" : " I won!!!");
		}

		return $this->_output->output(200, $output, true);
	}
}