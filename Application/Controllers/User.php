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


class User
{
    private $_db;
    private $_config;
    private $_params;
    private $_output;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_config = new Library\Config();
        $this->_db     = new Model\UserModel();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    /**
     * Covers the router's default method incase a part of the URL was missed
     *
     * @return array|string
     * @throws \Exception
     */
    public function index()
    {
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
        $details = [];

        if(isset($_POST))
        {
            $details = $_POST;
        }

        if(isset($details['return_output']))
        {
            $this->_output->setOutput($details['return_output']);
        }

        $query = $this->_db->register($details['name'], $details['password'], $details['email'], $details['status']);

        if($query == true)
        {
            return $this->_output->output(201, "user " . $details['name'] . " has successfully been registered", false);
        } else {
            return $this->_output->output(400, $query, false);
        }
    }

    /**
     * PUT - Allows a user to activate their account
     *
     * @return array|string Output either confirming activation was successful or an error
     * @throws \Exception
     */
    public function activate()
    {
        $user = $this->_params[0];
        $key  = $this->_params[1];
        $bot  = false;

        if(isset($this->_params[2]))
        {
            $bot = $this->_params[2];
        }

        if(isset($this->_params[3]))
        {
            $this->_output->setOutput($this->_params[3]);
        }

        if(isset($user) && is_string($user))
        {
            //there is a user specified, lets see if it is in the activation table
            $query = $this->_db->activate($user, $key);

            if($query == true)
            {
                return $this->_output->output(200, "User " . $user . " successfully activated!", $bot);
            }
            elseif($query == false)
            {
                return $this->_output->output(400, "Unable to activate, invalid key", $bot);
            } else {
                return $this->_output->output(400, $query, $bot);
            }
        } else {
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
        $user  = $this->_params[0];
        $mode  = (isset($this->_params[1])) ? $this->_params[1] : "all";
        $query = '';
        $bot   = false;

        if(isset($this->_params[2]))
        {
            $bot = $this->_params[2];
        }

        if(isset($this->_params[3]))
        {
            $this->_output->setOutput($this->_params[3]);
        }

        //check that $user is a string and not blank then pull from db
        if (isset($user) && is_string($user)) {

            //lets check the mode
            switch($mode)
            {
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

            if(is_array($query))
            {
                return $this->_output->output(200, $query, $bot);
            } elseif(empty($query)) {
                return $this->_output->output(404, "User $user not found", $bot);
            } else {
                return $this->_output->output(400, $query, $bot);
            }
        } else {
            return $this->_output->output(400, "The key 'user' must be defined as a string", $bot);
        }
    }

    /**
     * PUT - Adds new Twitch stats to user
     *
     * @return array|string Output either confirming successful addition of details or error
     * @throws \Exception
     */
    public function add_stats()
    {
        if(isset($_POST))
        {
            $stats = $_POST;

            if(isset($stats['return_output']))
            {
                $this->_output->setOutput($stats['return_output']);
            }

            $query = $this->_db->add_stats($stats['name'], $stats['followers'], $stats['views']);

            if($query == true)
            {
                return $this->_output->output(201, $stats['name'] . "'s stats has been put into the database", false);
            }
            elseif($query === false)
            {
                return $this->_output->output(500,'Hmm something went wrong, an administrator has been informed', false);
            } else {
                return $this->_output->output(400, $query, false);
            }
        }
    }
}