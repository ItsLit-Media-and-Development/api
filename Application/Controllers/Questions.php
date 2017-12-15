<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 06/12/2017
 * Time: 20:15
 */

namespace API\Controllers;

error_reporting(E_ALL);
use API\Library;

class Questions
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

    /**
     * Endpoint allows user's to add questions. Requires 2 URI parameters, 1) username 2) message
     * Example URL https://domain.com/Questions/add/$user/$message
     *
     * @return string JSON encoded array
     */
    public function add()
    {
        $user     = $this->_params[0];
        $question = $this->_params[1];
        $json     = '';

        if($user != '' && $question != '')
        {
            try {
                $stmt = $this->_db->prepare("INSERT INTO questions (user, question) VALUES (:user, :question)");
                $stmt->execute([
                    ':user' => $user,
                    ':question' => $question
                ]);

                if ($stmt->rowCount() > 0) {
                    $json = ['status' => 200, 'response' => "Question added"];
                }
            } catch (\PDOException $e) {
                $json = ["status" => 400, "response" => $e->getMessage()];
            }
        }

        return json_encode($json);
    }

    /**
     * Endpoint allows user to see list of questions that have been asked within the past 4 hours.
     * Example URL https://domain.com/Questions/showlist
     *
     * @return string
     */
    public function showlist()
    {
        $json = [];
        $stmt = $this->_db->prepare("SELECT user, question, date FROM questions WHERE date > SUBDATE( CURRENT_TIMESTAMP, INTERVAL 4 HOUR ) ORDER BY date ASC");
        $stmt->execute();

        $tmp = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        //lets actually check we have results!
        if(count($stmt) > 0)
        {
            $json = ['status' => 200, 'response' => $tmp];
        } else {
            $json = ['status' => 200, 'response' => 'There are currently no questions'];
        }
        return json_encode($json);
    }
}