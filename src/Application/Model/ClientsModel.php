<?php
/**
 * Clients Model Class
 *
 * All database functions regarding the Clients endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2018 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 1.1
 * @filesource
 */

namespace API\Model;

use API\Library;

class ClientsModel extends Library\BaseModel
{
    public function __construct()
    {
		parent::__construct();
    }

    public function modifyData($mode, $result)
    {
        switch($mode)
        {
            case 'add':
                if($result == 'win')
                {
                    $stmt = $this->_db->prepare("UPDATE winloss SET win = win + 1 WHERE id = 1");
                    $stmt->execute();
                } elseif($result == 'loss') {
                    $stmt = $this->_db->prepare("UPDATE winloss SET loss = loss + 1 WHERE id = 1");
                    $stmt->execute();
                }

                break;
            case 'remove':
                if($result == 'win')
                {
                    $stmt = $this->_db->prepare("UPDATE winloss SET win = win - 1 WHERE id = 1");
                    $stmt->execute();
                } elseif($result == 'loss') {
                    $stmt = $this->_db->prepare("UPDATE winloss SET loss = loss - 1 WHERE id = 1");
                    $stmt->execute();
                }
        }

    }

    public function getData()
    {
        $stmt = $this->_db->prepare("SELECT * FROM winloss");
        $stmt->execute();

        $this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $this->_output;
    }
}