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

        $id = $this->_params[0];

        //Check it is actually a number
        if(filter_var($id, FILTER_VALIDATE_INT) === false)
        {
            return $this->_output->output(400, "Post ID should be numeric", false);
        }

        //It must be so time to check if the post exists
        $blogPost['posts']    = $this->_db->getPost('ID', $id);
        $blogPost['comments'] = $this->_db->getComment($id);

        return $this->_output->output(200, $blogPost, false);
    }

    public function getPostBySlug()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }

        $slug = $this->_params[0];

        //Get the post then pull the comments
        $blogPost['posts'] = $this->_db->getPost('SLUG', $slug);

        //Make sure there is a post returned first!
        if(!$blogPost['posts'])
        {
            return $this->_output->output(404, "Blog Post {$slug} not found", false);
        }
        
        $blogPost['comments'] = $this->_db->getComment($blogPost['posts']['id']);

        return $this->_output->output(200, $blogPost, false);
    }

    public function listPosts()
    {
        if(!$this->authenticate(3)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('GET')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    public function updatePost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    public function createPost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('POST')) { return $this->_output->output(405, "Method Not Allowed", false); }
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

        return ($result) ? $this->_output->output(204, "", false) : $this->_output->output(404, "Blog Post not found", false);
    }

    public function approvePost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }

    public function unapprovePost()
    {
        if(!$this->authenticate(4)) { return $this->_output->output(401, 'Authentication failed', false); }
        if(!$this->expectedVerb('PUT')) { return $this->_output->output(405, "Method Not Allowed", false); }
    }
}