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

class Rewards
{
    private $_db;
    private $_config;
    private $_params;
    private $_output;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_config = new Library\Config();
        $this->_db     = $this->_config->database();
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

        if((isset($chan) && $chan != '') && (isset($reward) && $reward != '') && (isset($desc) && $desc != '')) {
            try {
                $stmt = $this->_db->prepare("INSERT INTO rewards(channel, name, description) VALUES(:chan, :reward, :desc)");
                $stmt->execute(
                    [
                        ':chan' => $chan,
                        ':reward' => $reward,
                        ':desc' => $desc
                    ]
                );

                if ($stmt->rowCount() > 0) {
                    return $this->_output->output(201, "Reward was added", $bot);
                } else {
                    return $this->_output->output(409, "Reward $reward already exists for $chan", $bot);
                }
            } catch (\PDOException $e) {
                return $this->_output->output(400, $e->getMessage(), $bot);
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
                //lets check the reward actually exists or is allowed
                try
                {
                    $stmt = $this->_db->prepare("SELECT id FROM rewards WHERE name = :name AND channel = :chan");
                    $stmt->execute(
                        [
                            ':chan' => $chan,
                            ':name' => $reward
                        ]
                    );
                    $row = $stmt->fetch();

                    if($stmt->rowCount() > 0)
                    {
                        $ins = $this->_db->prepare("INSERT INTO redemption(channel, user, reward, date) VALUES(:chan, :user, :id, DATE)");
                        $ins->execute(
                            [
                                ':chan' => $chan,
                                ':user' => $user,
                                ':id' => $row['id']
                            ]
                        );

                        if($ins->rowCount() > 0)
                        {
                            return $this->_output->output(201, "Redemption of " . $reward . " confirmed", $bot);
                        }
                    }
                } catch(\PDOException $e)
                {
                    return $this->_output->output(400, $e->getMessage(), $bot);
                }
            } else {
                return $this->redeem_code();
            }
        } else {
            //lets assume they want to know what can be redeemed!
            try {
                $stmt = $this->_db->prepare("SELECT name FROM rewards WHERE channel = :chan");
                $stmt->execute([':chan' => $chan]);
                $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if(count($res) > 0)
                {
                    return $this->_output->output(200, $res, $bot);
                } else {
                    return $this->_output->output(204, "OOPS! No rewards have been loaded!", $bot);
                }
            } catch(\PDOException $e) {
                return $this->_output->output(400, $e->getMessage(), $bot);
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

                try
                {
                    $stmt = $this->_db->prepare("INSERT INTO codes(title, code, platform, expiration) VALUES(:title, :code, :platform, :expiration)");
                    $stmt->execute(
                        [
                            ':title' => $title,
                            ':code' => $code,
                            ':platform' => $platform,
                            ':expiration' => $expires
                        ]
                    );

                    if($stmt->rowCount() > 0)
                    {
                        return $this->_output->output(201, "Addition of $title code confirmed");
                    }

                } catch(\PDOException $e)
                {
                    return $this->_output->output(400, $e->getMessage());
                }
            } else {
                return $this->_output->output(400, "The platform $platform is not accepted, please check the documentation for accepted platforms");
            }
        } else {
            return $this->_output->output(405, "Only POST methods are accepted for this endpoint", false);
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

        $user   = $this->_params[1];
        $reward = $this->_params[2];

        //lets see if we have any codes available
        try {
            $stmt = $this->_db->prepare("SELECT id, code FROM codes WHERE title = :title AND redeemed_by = NULL LIMIT 1");
            $stmt->execute([':title' => $reward]);

            $result = $stmt->fetch();

            if(empty($result))
            {
                return $this->_output->output(503, "Sorry there are no codes left for $reward", false);
            }

            $ins = $this->_db->prepare("UPDATE codes SET redeemed_by = :user WHERE id = :id");
            $stmt->execute(
                [
                    ':user' => $user,
                    ':id'   => $result['id']
                ]
            );

            if($ins->rowCount() > 0)
            {
                return $this->_output->output(200, "Congratulations on successfully redeeming your code for $reward, the code is " . $result['code'], false);
            }
        } catch(\PDOException $e) {
            return $this->_output->output(400, $e->getMessage(), false);
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

        try
        {
            $stmt = $this->_db->prepare("SELECT title, platform FROM codes WHERE redeemed_by = NULL AND expiration > CURRENT_TIMESTAMP ORDER BY expiration ASC");
            $stmt->execute();

            $tmp = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            //lets actually check we have results!
            if($stmt->rowCount() > 0)
            {
                return $this->_output->output(200, $tmp, false);
            } else
            {
                return $this->_output->output(200, "There are currently no questions", false);
            }
        } catch(\PDOException $e) {
            return $this->_output->output(400, $e->getMessage(), false);
        }
    }
}