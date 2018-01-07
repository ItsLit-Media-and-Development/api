<?php
/**
 * Questions Endpoint
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

error_reporting(E_ALL);
use API\Library;

class Questions
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
     * Adds questions to the system
     *
     * @return array|string Output either confirming submission or returning an error
     * @throws \Exception
     */
    public function add()
    {
        $channel  = $this->_params[0];
        $user     = $this->_params[1];
        $question = $this->_params[2];

        if(isset($this->_params[3]) && $this->_params[3] != '')
        {
            $this->_output->setOutput($this->_params[3]);
        }

        if($user != '' && $question != '')
        {
            try {
                $stmt = $this->_db->prepare("INSERT INTO questions (channel, user, question) VALUES (:channel, :user, :question)");
                $stmt->execute([
                    ':channel'  => $channel,
                    ':user'     => $user,
                    ':question' => $question
                ]);

                if ($stmt->rowCount() > 0) {
                    return $this->_output->output(200, "Question Added");
                }
            } catch (\PDOException $e) {
                return $this->_output->output(400, $e->getMessage());
            }
        } else {
            return $this->_output->output(400, "URI is missing all its parameters... Should look like https://api.itslit.uk/Questions/add/channel/username/question");
        }
    }

    /**
     * Marks a question as read
     *
     * @return array|string Output either confirming question marked as read or an error
     * @throws \Exception
     */
    public function read()
    {
        $qid = $this->_params[0];

        if(isset($this->_params[1]) && $this->_params[1] != '')
        {
            $this->_output->setOutput($this->_params[1]);
        }

        try {
            $stmt = $this->_db->prepare("UPDATE questions flag = 1 WHERE qid = :qid");
            $stmt->execute([':qid' => $qid]);

            if($stmt->rowCount() > 0)
            {
                return $this->_output->output(200, "Question is marked as read", false);
            }
        } catch (\PDOException $e) {
            return $this->_output->output(400, $e->getMessage(), false);
        }
    }

    /**
     * Reutrns all questions in the queue for the current user that was submitted in the past 4 hours
     *
     * @return array|string The output of the questions
     * @throws \Exception
     */
    public function showlist()
    {
        $chan = $this->_params[0];
        $bot = false;

        if(isset($this->_params[2]) && $this->_params[2] != '')
        {
            $this->_output->setOutput($this->_params[2]);
        }

        //are we saying that the response is not going to a bot
        if(isset($this->_params[1]) && $this->_params[1] != '')
        {
            $bot = $this->_params[1];
        }

        $stmt = $this->_db->prepare("SELECT qid, user, question, date FROM questions WHERE channel = :channel AND flag = 0 AND date > SUBDATE( CURRENT_TIMESTAMP, INTERVAL 4 HOUR ) ORDER BY date ASC");
        $stmt->execute([':channel' => $chan]);

        $tmp = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        //lets actually check we have results!
        if($stmt->rowCount() > 0)
        {
            return $this->_output->output(200, $tmp, $bot);
        } else {
            return $this->_output->output(200, "There are currently no questions", $bot);
        }
    }
}