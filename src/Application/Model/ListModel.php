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

    public function get_item($owner, $lName, $name)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT i.name, i.info FROM list_items i INNER JOIN lists l ON i.lid = l.lid WHERE l.owner = :owner AND list_name = :lName AND i.name = :name");

            $stmt->execute(
                [
                    ':owner' => $owner,
                    ':lName' => $lName,
                    ':name' => $name
                ]
            );

            $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
    public function get_list($owner, $lName, $qty)
    {
        if($qty == "all")
        {
            try
            {
                $stmt = $this->_db->prepare("SELECT i.name FROM list_items i INNER JOIN lists l ON i.lid = l.list_name WHERE l.owner = ':owner' AND l.list_name = ':lName'");

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
                    $stmt = $this->_db->prepare("SELECT i.name FROM list_items i INNER JOIN lists l ON i.lid = l.lid WHERE l.owner = :owner AND i.lid = :lName LIMIT $qty ORDER BY i.iid ASC");

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

    public function add_list($owner, $lName)
    {
        try
        {
            $stmt = $this->_db->prepare("INSERT INTO lists (list_name, owner) VALUES (:lName, :owner)");
            $stmt->execute([
                ':lName' => $lName,
                ':owner' => $owner
            ]);

            $this->_output = ($stmt->rowCount() > 0) ? true : false;

        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    //pass in owner for a check just to make sure we have the right list
    public function add_entry($owner, $lName, $name, $info = NULL)
    {
        try
        {
            $stmt = $this->_db->prepare("SELECT lid FROM lists WHERE list_name = :lName AND owner = :owner");
            $stmt->execute(
                [
                    ':lName' => $lName,
                    ':owner' => $owner
                ]
            );

            $lid = $stmt->fetch();

            $stmt2 = $this->_db->prepare("INSERT INTO list_items(lid, name, info) VALUES(:lid, :name, :info)");
            $stmt2->execute(
                [
                    ':lid' => $lid['lid'],
                    ':name' => $name,
                    ':info' => $info
                ]
            );

            $this->_output = ($stmt2->rowCount() > 0) ? true : false;
        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }
}