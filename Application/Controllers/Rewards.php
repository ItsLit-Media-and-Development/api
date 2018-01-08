<?php
/**
 * Rewards Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2017 Marc Towler
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

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_db     = new Model\RewardModel();
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
     * PUT - Adds rewards to the system
     *
     * @return array|string Output either confirming addition of reward or error
     * @throws \Exception
     */
    public function add_reward()
    {
        $chan    = $this->_params[0];
        $reward  = $this->_params[1];
        $desc    = $this->_params[2];
        $bot     = (isset($this->_params[3])) ? $this->_params[3] : true;

        if(isset($this->_params[4]))
        {
            $this->_output->setOutput($this->_params[4]);
        }

        if((isset($chan) && $chan != '') && (isset($reward) && $reward != '') && (isset($desc) && $desc != ''))
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
                return $this->_output->output(400, $query, $bot);
            }
        } else {
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
        $chan    = $this->_params[0];
        $user    = $this->_params[1];
        $reward  = $this->_params[2];
        $bot     = (isset($this->_params[3])) ? $this->_params[3] : false;

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

                if($query === true)
                {
                    return $this->_output->output(201, "Redemption of " . $reward . " confirmed", $bot);
                } else {
                    return $this->_output->output(400, $query, $bot);
                }
            } else {
                return $this->redeem_code();
            }
        } else {
            //lets assume they want to know what can be redeemed!
            $query = $this->_db->list_reward($chan);

            if($query != false)
            {
                return $this->_output->output(200, $query, $bot);
            } else {
                return $this->_output->output(204, "OOPS! No rewards have been loaded!", $bot);
            }
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
        $accepted = ['playstation', 'xbox', 'steam', 'gog', 'other'];

        if(isset($this->_params[1]))
        {
            $this->_output->setOutput($this->_params[1]);
        }

        if(isset($_POST))
        {
            $code = (isset($_POST['code'])) ? $_POST['code'] : NULL;
            $platform = (isset($_POST['platform'])) ? $_POST['platform'] : NULL;
            $title = (isset($_POST['title'])) ? $_POST['title'] : NULL;
            $expires = (isset($_POST['expires'])) ? $_POST['expires'] : NULL;

            if(is_null($code) || is_null($platform) || is_null($title) || is_null($expires))
            {
                return $this->_output->output(400, "The form was missing parameters, it needs: code, platform, title and expires to be valid", false);
            }

            if(in_array($platform, $accepted))
            {
                //lets strip out any hyphens to keep it consistent in the output
                $code = str_replace('-', '', $code);

                $query = $this->_db->add_code($title, $code, $platform, $expires);

                if($query === true)
                {
                    return $this->_output->output(201, "Addition of $title code confirmed");
                } else {
                    return $this->_output->output(400, $query);
                }
            } else {
                return $this->_output->output(400, "The platform $platform is not accepted, please check the documentation for accepted platforms");
            }
        } else {
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
        if(isset($this->_params[1]))
        {
            $this->_output->setOutput($this->_params[1]);
        }

        $query = $this->_db->list_code();

        if($query != false)
        {
            return $this->_output->output(200, $query, false);
        } else {
            return $this->_output->output(200, "There are currently no questions", false);
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
            return $this->_output->output(400, "Reward $reward could not be found, are you sure it is correct?", $bot);
        } else
        {
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
            return $this->_output->output(400, "The code $code could not be found, are you sure it is correct?", $bot);
        } else
        {
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
        if(isset($this->_params[3]))
        {
            return $this->_output->output(403, "Endpoint /Rewards/redeem cannot be used to redeem codes via a bot or Twitch chat for securite of the code");
        }

        $user = $this->_params[1];
        $reward = $this->_params[2];

        //lets see if we have any codes available
        $query = $this->_db->redeem_code($reward, $user);

        if($query != false)
        {
            return $this->_output->output(200, "Congratulations on successfully redeeming your code for $reward, the code is " . $query['code'], false);
        } else
        {
            return $this->_output->output(400, "OOPS! Something went wrong", false);
        }
    }
}