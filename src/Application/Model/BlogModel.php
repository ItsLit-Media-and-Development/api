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
                if($filter == '')
                {
                    $this->_output = false;
                }

                try
                {
                    $stmt = $this->_db->prepare("Select * from blog_post WHERE ID = :id");
                    $stmt->execute(
                        [
                            ':id' => $filter
                        ]
                    );

                    $this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);
                }
                catch(\PDOException $e)
                {
                    $this->_output = $e->getMessage();
                }

                break;
            
            case 'SLUG':
                if($filter == '')
                {
                    $this->_output = false;
                }
                
                try
                {
                    $stmt = $this->_db->prepare("Select * from blog_post WHERE slug = :slug");
                    $stmt->execute(
                        [
                            ':slug' => $filter
                        ]
                    );

                    $this->_output = $stmt->fetch(\PDO::FETCH_ASSOC);
                }
                catch(\PDOException $e)
                {
                    $this->_output = $e->getMessage();
                }

                break;

            default:
                try
                {
                    $stmt = $this->_db->prepare("Select * from blog_post");
                    $stmt->execute();

                    $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                }
                catch(\PDOException $e)
                {
                    $this->_output = $e->getMessage();
                }
        }

        return $this->_output;
    }

    public function addPost()
    {

    }

    public function updatePost()
    {

    }

    public function deletePost(int $id)
    {
        try 
        {
            $del = $this->_db->prepare("DELETE FROM blog_post WHERE id = :id");
            $del->execute(
                [
                    ':id' => $id
                ]
            );

            $this->_output = ($del->rowCount() > 0) ? true : false;
        } 
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function togglePostStatus()
    {

    }

    public function getComment(int $id)
    {
        try 
        {
            $stmt = $this->_db->prepare("SELECT response_id, display_name, email, comment, posted_on, approved, deleted FROM blog_comments WHERE bid = :id ORDER BY posted_on, bid");
            $stmt->execute(
                [
                    ':id' => $id
                ]
            );

            $this->_output = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } 
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function addComment(array $details)
    {
        try
        {
            $ins = $this->_db->prepare("INSERT INTO blog_comments (response_id, display_name, email, comment, approved) VALUES (:rid, :display, :email, :comment, :approved");
            $ins->execute(
                [
                    ':rid'      => (isset($details['response_id']) ? $details['response_id'] : 0),
                    ':display'  => $details['display_name'],
                    ':email'    => $details['email'],
                    ':comment'  => $details['comment'],
                    ':approved' => (isset($details['approved']) ? $details['approved'] : 0)
                ]
            );

            $this->_output = ($ins->rowCount() > 0) ? true : false;
        }
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    //A deleted comment is not truly deleted, it is essentially hidden so that sub comments dont break and the "this comment has been deleted" can appear to non-mods maybe
    public function deleteComment(int $id)
    {
        try 
        {
            $del = $this->_db->prepare("UPDATE blog_comments SET deleted = 1 WHERE bid = :id");
            $del->execute(
                [
                    ':id' => $id
                ]
            );

            $this->_output = ($del->rowCount() > 0) ? true : false;
        } 
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
    }

    public function toggleCommentStatus(int $id)
    {
        try 
        {
            $stmt = $this->_db->prepare("UPDATE blog_comments SET approved = 1 WHERE bid = :id");
            $stmt->execute(
                [
                    ':id' => $id
                ]
            );

            $this->_output = ($stmt->rowCount() > 0) ? true : false;
        } 
        catch(\PDOException $e)
        {
            $this->_output = $e->getMessage();
        }

        return $this->_output;
}