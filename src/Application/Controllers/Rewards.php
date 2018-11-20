<?php
/**
 * Rewards Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 0.3
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Rewards
{
    private $_db;
    private $_params;
    private $_output;
    private $_log;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_db     = new Model\RewardModel();
        $this->_params = $tmp->getAllParameters();
        $this->_output = new Library\Output();
        $this->_log = new Library\Logger();
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
        $this->_log->set_message("Rewards::main() Called from " . $_SERVER['REMOTE_ADDR'] . ", returning a 501", "INFO");

        return $this->_output->output(501, "Function not implemented", false);
    }

    /**
     * PUT - Adds rewards to the system
     *
     * @return array|string Output either confirming addition of reward or error
     * @throws \Exception
     */
    public function add_reward()
    {
        $this->_log->set_message("Rewards::add_reward() called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $chan    = $this->_params[0];
        $reward  = $this->_params[1];
        $desc    = $this->_params[2];
        $bot     = (isset($this->_params[3])) ? $this->_params[3] : true;
        $this->_output->setOutput((isset($this->_params[4])) ? $this->_params[4] : NULL);

        if(isset($desc) && $desc != '')
        {
            $query = $this->_db->add_reward($chan, $reward, $desc);

            if($query === true)
            {
                return $this->_output->output(201, "Reward was added", $bot);
            }
            elseif($query === false)
            {
                return $this->_output->output(409, "Reward $reward already exists for $chan", $bot);
            } else {
                $this->_log->set_message("Something went wrong with adding a reward, PDO error: $query", "ERROR");

                return $this->_output->output(400, $query, $bot);
            }
        } else {
            $this->_log->set_message("A parameter was missing, there is: $chan, $reward, $desc", "WARNING");

            return $this->_output->output(500, "Something was missing, check and try again", $bot);
        }
    }

    /**
     * PUT - Redeems a reward
     *
     * @return array|string Output either confirming the reward being redeemed or an error
     * @throws \Exception
     */
    public function redeem()
    {
        $this->_log->set_message("Rewards::redeem() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $chan    = $this->_params[0];
        $user    = $this->_params[1];
        $reward  = $this->_params[2];
        $bot = (isset($this->_params[3])) ? $this->_params[3] : true;

        if(isset($this->_params[4]))
        {
            $this->_output->setOutput($this->_params[4]);
        }

        //lets check that user and reward are set
        if((isset($user) && $user != '') && (isset($reward) && $reward != ''))
        {
            if($reward != 'code')
            {
                $query = $this->_db->redeem_reward($chan, $reward, $user);

                return ($query === true) ? $this->_output->output(201, "Redemption of " . $reward . " confirmed", $bot) : $this->_output->output(400, $query, $bot);
            } else {
                return $this->redeem_code();
            }
        } else {
            //lets assume they want to know what can be redeemed!
            $query = $this->_db->list_reward($chan);

            return ($query != false) ? $this->_output->output(200, $query, $bot) : $this->_output->output(204, "OOPS! No rewards have been loaded!", $bot);
        }
    }

    /**
     * POST - Allows addition of codes to the system
     *
     * @return array|string
     * @throws \Exception
     */
    public function add_code()
    {
        $this->_log->set_message("Rewards::add_code() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $accepted = ['playstation', 'xbox', 'steam', 'gog', 'other'];

        if(isset($_POST))
        {
            $code = (isset($_POST['code'])) ? $_POST['code'] : NULL;
            $platform = (isset($_POST['platform'])) ? $_POST['platform'] : NULL;
            $title = (isset($_POST['title'])) ? $_POST['title'] : NULL;
            $expires = (isset($_POST['expires'])) ? $_POST['expires'] : NULL;

            if(is_null($code) || is_null($platform) || is_null($title) || is_null($expires))
            {
                $this->_log->set_message("A parameter was missing, the following were passed: $code, $platform, $title, $expires", "WARNING");

                return $this->_output->output(400, "The form was missing parameters, it needs: code, platform, title and expires to be valid", false);
            }

            if(in_array($platform, $accepted))
            {
                $query = $this->_db->add_code($title, str_replace('-', '', $code), $platform, $expires);

                return ($query === true) ? $this->_output->output(201, "Addition of $title code confirmed") : $this->_output->output(400, $query);
            } else {
                $this->_log->set_message("An invalid platform was passed: $platform", "WARNING");

                return $this->_output->output(400, "The platform $platform is not accepted, please check the documentation for accepted platforms");
            }
        } else {
            $this->_log->set_message("Invalid attempt to access the method, it was not a POST request", "WARNING");

            return $this->_output->output(405, "Only POST methods are accepted for this endpoint", false);
        }
    }

    /**
     * Lists all available titles (with their platforms) that are still valid
     *
     * @return array|string Output list of titles and platforms or error
     * @throws \Exception
     */
    public function listcodes()
    {
        $this->_log->set_message("Rewards::listcodes() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        if(isset($this->_params[1]))
        {
            $this->_output->setOutput($this->_params[1]);
        }

        $query = $this->_db->list_code();

        if($query != false)
        {
            return $this->_output->output(200, $query, false);
        } else {
            return $this->_output->output(200, "There are currently no codes", false);
        }
    }

    /**
     * Removes selected reward from a channel
     *
     * @return array|string Output either confirming the removal or an error
     * @throws \Exception
     */
    public function remove_reward()
    {
        $this->_log->set_message("Rewards::remove_reward() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $chan = $this->_params[0];
        $reward = $this->_params[1];
        $bot = (isset($this->_params[2])) ? $this->_params[2] : false;

        if(isset($this->_params[3]))
        {
            $this->_output->setOutput($this->_params[3]);
        }

        $query = $this->_db->delete_reward($chan, $reward);

        if($query === true)
        {
            return $this->_output->output(200, "Reward $reward has been removed", $bot);
        } elseif($query === false)
        {
            $this->_log->set_message("Unknown reward of $reward was passed", "WARNING");

            return $this->_output->output(400, "Reward $reward could not be found, are you sure it is correct?", $bot);
        } else
        {
            $this->_log->set_message("Something went wrong, PDO error: $query", "ERROR");

            return $this->_output->output(400, $query, $bot);
        }
    }

    /**
     * Removes selected code
     *
     * @return array|string Output either confirming the removal or an error
     * @throws \Exception
     */
    public function remove_code()
    {
        $this->_log->set_message("Rewards::remove_code() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        $code = $this->_params[0];
        $bot = (isset($this->_params[1])) ? $this->_params[1] : false;

        if(isset($this->_params[2]))
        {
            $this->_output->setOutput($this->_params[2]);
        }

        $query = $this->_db->delete_code($code);

        if($query === true)
        {
            return $this->_output->output(200, "The code $code has been removed", $bot);
        } elseif($query === false)
        {
            $this->_log->set_message("Attempted to remove code $code but it did not exist?", "WARNING");

            return $this->_output->output(400, "The code $code could not be found, are you sure it is correct?", $bot);
        } else
        {
            $this->_log->set_message("Something went wrong, PDO error: $query", "ERROR");

            return $this->_output->output(400, $query, $bot);
        }
    }

    /**
     * Redeems a code
     *
     * @return array|string Output confirmation and the code received or an error
     * @throws \Exception
     */
    private function redeem_code()
    {
        $this->_log->set_message("Rewards::redeem_code() was called from " . $_SERVER['REMOTE_ADDR'], "INFO");

        if((!isset($this->_params[3])) && ($this->_params[3] == true))
        {
            $this->_log->set_message("An attempt to redeem a code via a bot was made, returning a 403", "WARNING");

            return $this->_output->output(403, "Endpoint /Rewards/redeem cannot be used to redeem codes via a bot or Twitch chat for securite of the code");
        }

        $user = $this->_params[1];
        $title = (isset($this->_params[5])) ? $this->_params[5] : false;

        //lets see if we have any codes available
        $query = $this->_db->redeem_code($title, $user);

        if($query != false)
        {
            if($query == "Sorry there are no codes left for $title")
            {
                return $this->_output->output(503, "Sorry there are no codes left for $title", false);
            }
            else
            {
                return $this->_output->output(200, "Congratulations on successfully redeeming your code for $title, the code is " . $query, false);
            }
        }
        else
        {
            $this->_log->set_message("Something went wrong", "WARNING");

            return $this->_output->output(400, "Sorry but there was no code available", false);
        }
    }
}