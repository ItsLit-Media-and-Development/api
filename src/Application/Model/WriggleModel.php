<?php
/**
 * Wriggle Model Class
 *
 * All database functions regarding the Wriggle endpoint is stored here
 *
 * @package        API
 * @author        Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright    Copyright (c) 2017 Marc Towler
 * @license        https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link        https://api.itslit.uk
 * @since        Version 0.8
 * @filesource
 */

namespace API\Model;

use API\Library;

class WriggleModel
{
    private $_db;
    private $_config;
    private $_output;

    public function __construct()
    {
        $this->_config = new Library\Config();
        $this->_db = $this->_config->database();
    }

    public function add_draw($user, $card)
    {
        try
        {
            $stmt = $this->_db->prepare("INSERT INTO wrig_draw (user, card) VALUES (:user, :card)");
            $stmt->execute([
                ':user' => $user,
                ':card' => $card
            ]);

            $this->_output = ($stmt->rowCount() > 0) ? true : false;

        } catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function list_draw($card)
    {
        if(is_array($card))
        {
            $array = '';

            foreach($card as $c)
            {
                if($array == '')
                {
                    $array = "card = '$c'";
                }
                else
                {
                    $array .= " OR card = '$c'";
                }
            }
            try
            {
                $stmt = $this->_db->prepare("SELECT user, card FROM wrig_draw WHERE $array ORDER BY cid ASC LIMIT 2");
                $stmt->execute();

                $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            } catch(\PDOException $e)
            {
                $this->_output = $e->getMessage();
            }
        }
        elseif($card == "all")
        {
            try
            {
                $stmt = $this->_db->prepare("SELECT user, card FROM wrig_draw ORDER BY cid ASC LIMIT 2");
                $stmt->execute();

                $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch(\PDOException $e)
            {
                $this->_output = $e->getMessage();
            }
        }
        else
        {
            try
            {
                $stmt = $this->_db->prepare("SELECT user, card FROM wrig_draw WHERE card = :card ORDER BY cid ASC");
                $stmt->execute([':card' => $card]);

                $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch(\PDOException $e)
            {
                $this->_output = $e->getMessage();
            }
        }

        return $this->_output;
    }
}