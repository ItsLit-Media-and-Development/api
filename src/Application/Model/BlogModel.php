<?php
/**
 * Blog Model Class
 *
 * All database functions regarding the Blog endpoint is stored here
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2023 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 2.0
 * @filesource
 */

namespace API\Model;

use API\Library;

class BlogModel extends Library\BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}

    public function getPost($filterType = '', $filter = '')
    {
        switch ($filterType)
        {
            case 'ID':
                $stmt = $this->_db->prepare("Select * from blog_post WHERE ID = :id");
                $stmt->execute(
                    [
                        ':id' => $filter
                    ]
                );

                $this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);
                break;
            
            case 'SLUG':
                return "My slug is {$filter}";
                break;

            default:
        }

        return $this->_output;
    }
}