<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 10/12/2017
 * Time: 10:38
 */

namespace API\Library;


class Router
{
    private $segments   = array();
    private $parameters = array();

    public function __construct()
    {
        $this->getSegments();
        $this->getParameters();
    }

    private function getURI()
    {
        return rtrim(substr($_SERVER['REQUEST_URI'], 1), '/');
    }

    private function getSegments()
    {
        $this->segments = explode('/', $this->getURI());
    }

    public function getController()
    {
        return (isset($this->segments[0])) ? $this->segments[0] : 'index';
    }

    public function getMethod()
    {
        return (isset($this->segments[1])) ? $this->segments[1] : 'index';
    }

    private function getParameters()
    {
        if(is_array($this->segments))
        {
            $parameters = (count($this->segments) > 2) ? array_slice($this->segments, 2) : false;

            if(!$parameters)
            {
                return false;
            }

            //remove empty parameters
            $parameters = array_diff($parameters, array(''));

            //reindex the array
            $parameters = array_values($parameters);

            $this->parameters = $parameters;
        }
    }

    public function getParameter($index)
    {
        return (is_array($this->parameters) && isset($this->parameters[$index])) ? $this->parameters[$index] : false;
    }

    public function getAllParameters()
    {
        return (!empty($this->parameters)) ? $this->parameters : false;
    }
}