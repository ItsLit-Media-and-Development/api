<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 14/02/2018
 * Time: 18:22
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