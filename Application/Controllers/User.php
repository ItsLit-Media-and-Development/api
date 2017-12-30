<?php


namespace API\Controllers;

use API\Library;


class User
{
    private $_db;
    private $_hash;
    private $_config;
    private $_params;
    private $_output;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_config = new Library\Config();
        $this->_hash   = new Library\Password();
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

    //POST

    /**
     *
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

                //$json = ["status" => 201, "response" => "user " . $details['name'] . " has successfully been registered"];
                return $this->_output->output(201, "user " . $details['name'] . " has successfully been registered", false);
            }
        } catch(\PDOException $e) {
            //$json = ["status" => 400, "response" => $e->getMessage()];
            $this->_output->output(400, $e->getMessage(), false);
        }
    }

    //PUT
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
                        //$json = ["status" => 200, "response" => "User " . $user . " successfully activated!"];
                        return $this->_output->output(200, "User " . $user . " successfully activated!", $bot);
                    } else {
                        //$json = ["status" => 400, "response" => "Unable to activate, invalid key"];
                        return $this->_output->output(400, "Unable to activate, invalid key", $bot);
                    }
                }
            } else {
                //$json = ["status" => 400, "response" => "The key 'user' must be defined as a string"];
                return $this->_output->output(400, "The key 'user' must be defined as a string", $bot);
            }
        } catch(\PDOException $e) {
            //$json = ["status" => 400, "response" => $e->getMessage()];
            return $this->_output->output(400, $e->getMessage(), $bot);
        }
    }

    //GET
    public function profile()
    {
        $user = $this->_params[0];
        $mode = (isset($this->_params[1])) ? $this->_params[1] : "all";
        $sql  = '';
        $bot  = false;

        if(isset($this->_params[2]))
        {
            $bot = $this->_params[2];
        }

        if(isset($this->_params[3]))
        {
            $this->_output->setOutput($this->_params[3]);
        }

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
                    //$json = ["status" => 200, "response" => $stmt->fetchAll(\PDO::FETCH_ASSOC)];
                    return $this->_output->output(200, $stmt->fetchAll(\PDO::FETCH_ASSOC), $bot);
                } else {
                    //$json = ["status" => 404, "response" => "User not found"];
                    return $this->_output->output(404, "User $user not found", $bot);
                }
            } else {
                //$json = ["status" => 400, "response" => "The key 'user' must be defined as a string"];
                return $this->_output->output(400, "The key 'user' must be defined as a string", $bot);
            }
        } catch(\PDOException $e) {
            //$json = ["status" => 400, "response" => $e->getMessage()];
            return $this->_output->output(400, $e->getMessage(), $bot);
        }
    }

    public function add_stats()
    {
        if(isset($_POST))
        {
            $stats = $_POST;

            if(isset($details['return_output']))
            {
                $this->_output->setOutput($stats['return_output']);
            }

            try {
                $usr = $this->_db->prepare("SELECT ID FROM users WHERE name = :name");
                $usr->execute([':name' => $stats['name']]);

                $tmp = $usr->fetch();

                $stmt = $this->_db->prepare("INSERT INTO monthly_stats (uid, date, followers, views) VALUES (:id, :date, :follow, :views)");
                $res = $stmt->execute(
                    [
                        ':id'     => $tmp["ID"],
                        ':date'   => date('Y-m-d'),
                        ':follow' => $stats['followers'],
                        ':views'  => $stats['views']
                    ]
                );

                if($res)
                {
                    //$json = ['status' => 201, 'response' => $stats['name'] . "'s has been put into the database"];
                    return $this->_output->output(201, $stats['name'] . "'s stats has been put into the database", false);
                } else {
                    //$json = ['status' => 500, 'response' => 'Hmm somthing went wrong, an administrator has been informed'];
                    return $this->_output->output(500,'Hmm something went wrong, an administrator has been informed', false);
                }
            } catch(\PDOException $e) {
                //$json = ["status" => 400, "response" => $e->getMessage()];
                return $this->_output->output(400, $e->getMessage(), false);
            }
        }
    }
}