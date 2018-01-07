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

use API\Library;
use API\Model;

class Questions
{
    private $_db;
    private $_params;
    private $_output;

    public function __construct()
    {
        $tmp           = new Library\Router();
        $this->_db     = new Model\QuestionModel();
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
            $query = $this->_db->add_question($channel, $user, $question);
            if(!is_string($query) && $query == true)
            {
                return $this->_output->output(200, "Question Added");
            } else {
                return $this->_output->output(400, $query);
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

        $query = $this->_db->mark_read($qid);

        if($query)
        {
            return $this->_output->output(200, "Question is marked as read", false);
        } else {
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

        $query = $this->_db->list_questions($chan);

        //lets actually check we have results!
        if(is_array($query))
        {
            return $this->_output->output(200, $query, $bot);
        } else {
            return $this->_output->output(200, "There are currently no questions", $bot);
        }
    }
}