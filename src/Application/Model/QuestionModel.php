<?php
/**
 * Questions Model Class
 *
 * All database functions regarding the Questions endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since		Version 0.3
 * @filesource
 */

namespace API\Model;

use API\Library;

class QuestionModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

    public function add_question($channel, $user, $question)
    {
        try {
            $stmt = $this->_db->prepare("INSERT INTO questions (channel, user, question) VALUES (:channel, :user, :question)");
            $stmt->execute([
                ':channel' => $channel,
                ':user' => $user,
                ':question' => $question
            ]);

            $this->_output = ($stmt->rowCount() > 0) ? true : false;

        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function mark_read($qid)
    {
        $stmt = $this->_db->prepare("UPDATE questions flag = 1 WHERE qid = :qid");
        $stmt->execute([':qid' => $qid]);

        if($stmt->rowCount() > 0)
        {
            $this->_output = true;
        } else {
            $this->_output = false;
        }

        return $this->_output;
    }

    public function list_questions($chan)
    {
        try {
            $stmt = $this->_db->prepare("SELECT qid, user, question, date FROM questions WHERE channel = :channel AND flag = 0 AND date > SUBDATE( CURRENT_TIMESTAMP, INTERVAL 4 HOUR ) ORDER BY date ASC");
            $stmt->execute([':channel' => $chan]);

            $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
}