<?php
/**
 * Blog Endpoint
 *
 * @package		API
 * @author		Marc Towler <marc@marctowler.co.uk>
 * @copyright	Copyright (c) 2023 Marc Towler
 * @license		https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link		https://api.itslit.uk
 * @since       Version 2.0
 * @filesource
 */

namespace API\Controllers;

use API\Library;
use API\Model;

class Blog extends Library\BaseController
{
    protected $_db;

    public function __construct()
    {
		parent::__construct();

		$this->_db = new Model\BlogModel();
    }

    public function getPostByID()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id       = $this->_params[0];
        $approved = isset($this->_params[1]) && $this->_params[1] === true ? (bool) $this->_params[1] : false;

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Post ID should be numeric", false);
        }

        //It must be so time to check if the post exists
        $blogPost['post'] = $this->_db->getPost($approved, 'ID', $id);

        if($blogPost['post'] === false)
        {
            return $this->_output->output(404, "Blog Post not found", false);
        }

        $blogPost['post']['comments'] = $this->_db->getComment($id);
        $blogPost['post']['tags']     = $this->_db->getTags($id);

        return $this->_output->output(200, $blogPost, false);
    }

    public function getPostBySlug()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $slug     = $this->_params[0];
        $approved = isset($this->_params[1]) ? $this->_params[1] : false;

        //Get the post then pull the comments
        $blogPost['post'] = $this->_db->getPost($approved, 'SLUG', $slug);

        //Make sure there is a post returned first!
        if($blogPost['post'] === false)
        {
            return $this->_output->output(404, "Blog Post {$slug} not found", false);
        }
        
        $blogPost['post']['comments'] = $this->_db->getComment($blogPost['post']['id']);
        $blogPost['post']['tags']     = $this->_db->getTags($blogPost['post']['id']);

        return $this->_output->output(200, $blogPost, false);
    }

    public function listPosts()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $approved = isset($this->_params[0]) ? $this->_params[0] : false;

        $posts = $this->_db->getPost($approved);

        return $this->_output->output(200, $posts, false);
    }

    public function updatePost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        $output = $this->_db->updatePost($data);

        //Check to see if we had an error
        if(!is_bool($output))
        {
            return $this->_output->output(500, $output, false);
        }

        if($output === false)
        {
            return $this->_output->output(400, $output, false);
        }

        return $this->_output->output(200, $output, false);
    }

    public function createPost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        $output = $this->_db->addPost($data);

        //Check to see if we had an error
        if(!is_bool($output))
        {
            return $this->_output->output(500, $output, false);
        }

        if($output === false)
        {
            return $this->_output->output(400, $output, false);
        }

        return $this->_output->output(200, $output, false);
    }

    public function deletePost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('DELETE')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Post ID should be numeric", false);
        }

        $result = $this->_db->deletePost($id);

        return ($result) ? $this->_output->output(204, null, false) : $this->_output->output(404, "Blog Post not found", false);
    }

    public function approvePost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PATCH')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['id']) || empty($data))
        {
            return $this->_output->output(400, "No Data Sent", false);
        } 
        elseif(filter_var($data['id'], FILTER_VALIDATE_INT) === false) 
        {
            return $this->_output->output(400, "ID Should be Numeric", false);
        }

        $output = $this->_db->togglePostStatus($data['id']);

        return $this->_output->output(200, $output, false);
    }

    public function unapprovePost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PATCH')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['id']) || empty($data))
        {
            return $this->_output->output(400, "No Data Sent", false);
        } 
        elseif(filter_var($data['id'], FILTER_VALIDATE_INT) === false) 
        {
            return $this->_output->output(400, "ID Should be Numeric", false);
        }

        $output = $this->_db->togglePostStatus($data['id']);

        return $this->_output->output(200, $output, false);
    }

    public function createComment()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        //Quick check to make sure the data is not empty
        if(!isset($data) || empty($data))
        {
            return $this->_output->output(400, "No Data sent", false);
        }

        $output = $this->_db->addComment($data);

        //Check to see if we had an error
        if(!is_bool($output))
        {
            return $this->_output->output(500, $output, false);
        }

        if($output === false)
        {
            return $this->_output->output(400, $output, false);
        }

        return $this->_output->output(200, $output, false);
    }

    public function deleteComment()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('DELETE')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($data['id'], FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Post ID should be numeric", false);
        }

        $result = $this->_db->deleteComment($id);

        return ($result) ? $this->_output->output(204, null, false) : $this->_output->output(404, "Blog Post not found", false);
    }

    public function approveComment()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PATCH')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['id']) || empty($data))
        {
            return $this->_output->output(400, "No Data Sent", false);
        } 
        elseif(filter_var($data['id'], FILTER_VALIDATE_INT) === false) 
        {
            return $this->_output->output(400, "ID Should be Numeric", false);
        }

        $output = $this->_db->toggleCommentStatus($data['id']);

        return $this->_output->output(200, $output, false);
    }

    public function unapproveComment()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PATCH')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['id']) || empty($data))
        {
            return $this->_output->output(400, "No Data Sent", false);
        } 
        elseif(filter_var($data['id'], FILTER_VALIDATE_INT) === false) 
        {
            return $this->_output->output(400, "ID Should be Numeric", false);
        }

        $output = $this->_db->toggleCommentStatus($data['id']);

        return $this->_output->output(200, $output, false);
    }
}