<?php
/**
 * List Model Class
 *
 * All database functions regarding the Lists endpoint is stored here
 *
 * @package       API
 * @author        Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright     Copyright (c) 2017 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 0.8
 * @filesource
 */

namespace API\Model;

use API\Library;

class ListModel
{
    private $_db;
    private $_config;
    private $_output;

    public function __construct()
    {
        $this->_config = new Library\Config();
        $this->_db = $this->_config->database();
    }

    public function get_list($owner, $lName, $qty)
    {
        if($qty == "all")
        {
            try
            {
                $stmt = $this->_db->prepare("SELECT i.name, i.info FROM list_items i INNER JOIN lists l ON i.lName = l.list_name WHERE l.owner = :owner AND i.lName = :lName ORDER BY i.iid ASC");

                $stmt->execute(
                    [
                        ':owner' => $owner,
                        ':lName' => $lName
                    ]
                );

                $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch(\PDOException $e)
            {
                $this->_output = $e->getMessage();
            }
        }
        else
        {
            if(is_int($qty))
            {
                try
                {
                    $stmt = $this->_db->prepare("SELECT i.name, i.info FROM list_items i INNER JOIN lists l ON i.lName = l.list_name WHERE l.owner = :owner AND i.lName = :lName LIMIT $qty ORDER BY i.iid ASC");

                    $stmt->execute(
                        [
                            ':owner' => $owner,
                            ':lName' => $lName
                        ]
                    );

                    $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                } catch(\PDOException $e)
                {
                    $this->_output = $e->getMessage();
                }
            }
        }

        return $this->_output;
    }
}