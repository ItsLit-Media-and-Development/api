<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 12/12/2017
 * Time: 07:41
 */

namespace API\Controllers;


class User
{
    private $_db;
    private $_hash;
    private $_config;

    public function __construct()
    {
        $this->_config = new Library\Config();
        $this->_hash   = new Library\Password();
        $this->_db     = $this->_config->database();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    public function index()
    {
        $ret = ['status' => 501, 'response' => 'function not implemented'];

        return json_encode($ret);
    }

    //GET
    public function profile($user, $mode = 'all')
    {
        $json = [];
        $sql  = '';

        try {
            //check that $user is a string and not blank then pull from db
            if (isset($user) && is_string($user)) {

                //lets check the mode
                switch($mode)
                {
                    case "all":
                        $sql ="SELECT u.name, u.email, s.date, s.followers, s.views FROM users u INNER JOIN monthly_stats s ON u.id = s.uid WHERE name = :name";

                        break;

                    case "followers":
                        $sql ="SELECT u.name, u.email, s.date, s.followers FROM users u INNER JOIN monthly_stats s ON u.id = s.uid WHERE name = :name";

                        break;

                    case "views":
                        $sql = "SELECT u.name, u.email, s.date, s.views FROM users u INNER JOIN monthly_stats s ON u.id = s.uid WHERE name = :name";

                        break;
                }

                $stmt = $this->_db->prepare($sql);
                $stmt->execute([':name' => $user]);

                if($stmt->rowCount() > 0)
                {
                    $json = ["status" => 200, "response" => $stmt->fetchAll(\PDO::FETCH_ASSOC)];
                } else {
                    $json = ["status" => 404, "response" => "User not found"];
                }
            } else {
                $json = ["status" => 400, "response" => "The key 'user' must be defined as a string"];
            }
        } catch(\PDOException $e) {
            $json = ["status" => 400, "response" => $e->getMessage()];
        }

        return json_encode($json);
    }
}
}