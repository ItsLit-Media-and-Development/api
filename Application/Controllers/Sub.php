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

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_config = new Library\Config();
        $this->_db     = $this->_config->database();
        $this->_params = $tmp->getAllParameters();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function index()
    {
        $ret = array('status' => 501, 'response' => 'function not implemented');

        return json_encode($ret);
    }

    public function add_reward()
    {
        $json    = [];
        $chan    = $this->_params[0];
        $reward  = $this->_params[1];
        $desc    = $this->_params[2];

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
                    $json = ['status' => 201, 'response' => "Reward $reward was added!"];
                } else {
                    $json = ['status' => 409, 'response' => "Reward $reward already exists for $chan"];
                }
            } catch (\PDOException $e) {
                $json = ["status" => 400, "response" => $e->getMessage()];
            }
        } else {
            $json = ["status" => 500, "response" => "Something was missing, check and try again"];
        }
        return json_encode($json);
    }

    public function redeem()
    {
        $json    = [];
        $chan    = $this->_params[0];
        $user    = $this->_params[1];
        $reward  = $this->_params[2];

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
                        $json = ["status" => 201, "response" => "Redemption of " . $reward . " confirmed"];
                    }
                }
            } catch (\PDOException $e) {
                $json = ["status" => 400, "response" => $e->getMessage()];
            }
        } else {
            //lets assume they want to know what can be redeemed!
            try {
                $stmt = $this->_db->prepare("SELECT name FROM rewards WHERE channel = :chan");
                $stmt->execute([':chan' => $chan]);
                $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if(count($res) > 0)
                {
                    $json = ["status" => 200, "response" => $res];
                } else {
                    $json = ["status" => 204, "response" => "OOPS! No rewards have been loaded!"];
                }
            } catch(\PDOException $e) {
                $json = ["status" => 400, "response" => $e->getMessage()];
            }
        }

        return json_encode($json);
    }

    public function tier($user = '')
    {
        return json_encode($user);
    }
}