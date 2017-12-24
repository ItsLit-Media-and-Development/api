<?php


namespace API\Controllers;

use API\Library;


class User
{
    private $_db;
    private $_hash;
    private $_config;
    private $_params;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_config = new Library\Config();
        $this->_hash   = new Library\Password();
        $this->_db     = $this->_config->database();
        $this->_params = $tmp->getAllParameters();

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

    //POST

    /**
     *
     * @return string
     */
    public function register()
    {
        $json    = [];
        $details = [];

        if(isset($_POST))
        {
            $details = $_POST;
        }

        try {
            if(isset($details['password']) && $details['password'] != '')
            {
                $details['password'] = $this->_hash->password_hash($details['password'], PASSWORD_BCRYPT);

                $stmt = $this->_db->prepare("INSERT INTO users(name, email, password, status) VALUES (:name, :pass, :email, :status)");
                $stmt->execute([
                    ':name'   => $details['name'],
                    ':pass'   => $details['password'],
                    ':email'  => $details['email'],
                    ':status' => $details['status']
                ]);

                $json = ["status" => 201, "response" => "user " . $details['name'] . " has successfully been registered"];
            }
        } catch(\PDOException $e) {
                $json = ["status" => 400, "response" => $e->getMessage()];
        }

        return json_encode($json);
    }

    //PUT
    public function activate()
    {
        $user = $this->_params[0];
        $key  = $this->_params[1];
        $json = [];

        try {
            if(isset($user) && is_string($user))
            {
                //there is a user specified, lets see if it is in the activation table
                $stmt = $this->_db->prepare("SELECT key FROM activation WHERE name = :name");
                $stmt->execute([':name' => $user]);
                $row = $stmt->fetch();

                //is there a result? If so, check the retrieved key against what we have
                if(is_array($row))
                {
                    if($row['key'] == $key)
                    {
                        //remove the key
                        $stmt2 = $this->_db->prepare("DELETE key FROM activation WHERE name = :name");
                        $stmt2->execute([':name' => $user]);

                        //update status flag

                        //return 200
                        $json = ["status" => 200, "response" => "User " . $user . " successfully activated!"];
                    } else {
                        $json = ["status" => 400, "response" => "Unable to activate, invalid key"];
                    }
                }
            } else {
                $json = ["status" => 400, "response" => "The key 'user' must be defined as a string"];
            }
        } catch(\PDOException $e) {
            $json = ["status" => 400, "response" => $e->getMessage()];
        }

        return json_encode($json);
    }

    //GET
    public function profile()
    {
        $user = $this->_params[0];
        $mode = (isset($this->_params[1])) ? $this->_params[1] : "all";
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

    public function add_stats()
    {
        $json  = [];

        if(isset($_POST))
        {
            $stats = $_POST;

            try {
                $usr = $this->_db->prepare("SELECT ID FROM users WHERE name = :name");
                $usr->execute([':name' => $stats['name']]);

                $tmp = $usr->fetch();

                $stmt = $this->_db->prepare("INSERT INTO monthly_stats (uid, date, followers, views) VALUES (:id, :date, :follow, :views)");
                $res = $stmt->execute(
                    [
                        ':id'     => $tmp,
                        ':date'   => date('Y-m-d'),
                        ':follow' => $stats['followers'],
                        ':views'  => $stats['views']
                    ]
                );

                if($res)
                {
                    $json = ['status' => 201, 'response' => $stats['name'] . "'s has been put into the database"];
                } else {
                    $json = ['status' => 500, 'response' => 'Hmm somthing went wrong, an administrator has been informed'];
                }
            } catch(\PDOException $e) {
                $json = ["status" => 400, "response" => $e->getMessage()];
            }
        }

        return json_encode($json);
    }
}