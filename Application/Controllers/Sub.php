<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 29/10/2017
 * Time: 15:57
 */

namespace API\Controllers;

use API\Library;

class Sub
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

    public function index()
    {
        return $this->_output->output(501, "Function not implemented", false);
    }

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
            //lets check the reward actually exists or is allowed
            try {
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
                            ':id'   => $row['id']
                        ]
                    );

                    if($ins->rowCount() > 0)
                    {
                        return $this->_output->output(201, "Redemption of " . $reward . " confirmed", $bot);
                    }
                }
            } catch (\PDOException $e) {
                return $this->_output->output(400, $e->getMessage(), $bot);
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

    public function tier($user = '')
    {
        return $this->_output->output(501, "Function not implemented", false);
    }
}