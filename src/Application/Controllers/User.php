<?php
/**
 * User Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2017 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 0.1
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;
use GuzzleHttp\Client;


class User
{
    private $_db;
    private $_config;
    private $_params;
    private $_output;
    private $_log;
	private $_guzzle;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_config = new Library\Config();
        $this->_db     = new Model\UserModel();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_log = new Library\Logger();
		$this->_guzzle = new Client();
    }

    public function __destruct()
    {
        $this->_log->saveMessage();
    }

    /**
     * Covers the router's default method incase a part of the URL was missed
     *
     * @return array|string
     * @throws \Exception
     */
    public function main()
    {
        $this->_log->set_message("User::main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }


    /**
     * POST - adds user's to the system
     *
     * @return array|string Outputs either confirmation of registration or error
     * @throws \Exception
     */
    public function register()
    {
        $this->_log->set_message("User::register() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $details = isset($_POST) ? $_POST : [];

        if(isset($details['return_output']))
        {
            $this->_output->setOutput($details['return_output']);
        }

        $query = $this->_db->register($details['name'], $details['password'], $details['email'], $details['status']);

        return ($query == true) ? $this->_output->output(201, "user " . $details['name'] . " has successfully been registered", false) : $this->_output->output(400, $query, false);
    }

    /**
     * PUT - Allows a user to activate their account
     *
     * @return array|string Output either confirming activation was successful or an error
     * @throws \Exception
     */
    public function activate()
    {
        $this->_log->set_message("User::activate() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $user = $this->_params[0];
        $key  = $this->_params[1];
        $bot = (isset($this->_params[2])) ? $this->_params[2] : false;

        $this->_output->setOutput((isset($this->_params[3])) ? $this->_params[3] : NULL);

        if(isset($user) && is_string($user))
        {
            //there is a user specified, lets see if it is in the activation table
            $query = $this->_db->activate($user, $key);

            return ($query == true) ? $this->_output->output(200, "User " . $user . " successfully activated!", $bot) : ($query == false) ? $this->_output->output(400, "Unable to activate, invalid key", $bot) : $this->_output->output(400, $query, $bot);
        } else {
            $this->_log->set_message("User::register() No uer key was definied, returning a 400", "WARNING");

            return $this->_output->output(400, "The key 'user' must be defined as a string", $bot);
        }
    }

    /**
     * GET - Returns a user's profile (username, email, view and follow counts)
     *
     * @return array|string Outputs either the returned data or an error
     * @throws \Exception
     */
    public function profile()
    {
        $this->_log->set_message("User::profile() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $user  = $this->_params[0];
        $mode  = (isset($this->_params[1])) ? $this->_params[1] : "all";
        $query = '';
        $bot = (isset($this->_params[2])) ? $this->_params[2] : false;

        $this->_output->setOutput((isset($this->_params[3])) ? $this->_params[3] : NULL);

		//lets check the mode
		switch($mode) {
			case "all":
				$query = $this->_db->profile_all($user);

				break;
			case "followers":
				$query = $this->_db->profile_follow($user);

				break;
			case "views":
				$query = $this->_db->profile_views($user);

				break;
		}

		return (is_array($query)) ? $this->_output->output(200, $query, $bot) :
			(empty($query)) ? $this->_output->output(404, "User $user not found", $bot) :
				$this->_output->output(400, $query, $bot);
            return (is_array($query)) ? $this->_output->output(200, $query, $bot) : (empty($query)) ? $this->_output->output(404, "User $user not found", $bot) : $this->_output->output(400, $query, $bot);
		/*} else {
			$this->_log->set_message("User::profile() No uer key was definied, returning a 400", "WARNING");

			return $this->_output->output(400, "The key 'user' must be defined as a string", $bot);
		}*/
    }

    /**
     * PUT - Adds new Twitch stats to user
     *
     * @return array|string Output either confirming successful addition of details or error
     * @throws \Exception
     */
    public function add_stats()
    {
        $this->_log->set_message("User::add_stats() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        if(isset($_POST))
        {
            $stats = $_POST;

            $this->_output->setOutput((isset($stats['return_output'])) ? $stats['return_output'] : NULL);

            $query = $this->_db->add_stats($stats['name'], $stats['followers'], $stats['views']);

            if($query == true)
            {
                return $this->_output->output(201, $stats['name'] . "'s stats has been put into the database", false);
            }
            elseif($query === false)
            {
                $this->_log->set_message("Something went wrong with adding stats, $query", "ERROR");

                return $this->_output->output(500,'Hmm something went wrong, an administrator has been informed', false);
            } else {
                $this->_log->set_message("Something went wrong in User::add_stats(), PDO error $query", "ERROR");

                return $this->_output->output(400, $query, false);
            }
        }
    }

	public function get_stats()
	{
		$this->_log->set_message("User::get_stats() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

		$user = $this->_params[0];

		$follows = $this->_guzzle->request('GET', 'https://api.itslit.uk/twitch/totalfollowers/' . $user . '/true');
		$follows = $follows->getBody();
		$views = $this->_guzzle->get('https://api.itslit.uk/twitch/totalviews/' . $user . '/true');
		$views = $views->getBody();

		$output = ['output' => ["title" => "Total stats for $user:", "followers" => "Followers: $follows",
								"Views" => "Total Views: $views"]];

		$this->_output->setOutput('html');

		return $this->_output->output(200, $output, false);
	}
}