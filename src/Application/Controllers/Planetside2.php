<?php
namespace API\Controllers;

use API\Library;
use API\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class Planetside2 extends Library\BaseController
{
    private $_guzzle;
	public function __construct()
	{
		parent::__construct();

		$this->_guzzle = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));
    }

    public function kdr()
    {
        $user = strtolower($this->_params[0]);

        try {
            $request = $this->_guzzle->get("https://census.daybreakgames.com/s:itslittany/get/ps2:v2/character/?name.first_lower=$user&c:resolve=outfit_member_extended&c:resolve=stat_history&c:resolve=online_status&c:resolve=world&c:join=world");

            $output = json_decode($request->getBody(), true);
            $output = $output['character_list'][0]['stats']['stat_history'];

            $kills = 0;
            $deaths = 0;

            for($i = 0; $i < sizeof($output); $i++)
            {
                if($output[$i]['stat_name'] == "deaths")
                {
                    $deaths = $output[$i]['all_time'];
                } else if($output[$i]['stat_name'] == "kills") {
                    $kills = $output[$i]['all_time'];
                }
            }

            return $this->_output->output(200, substr(($kills/$deaths), 0, 4), true);
        } catch(RequestException $e) {
            if ($e->getResponse()->getStatusCode() == '400') {
                $output = json_decode((string) $e->getResponse()->getBody(), true)['message'];
            }

        }
    }

    public function adr()
    {
        $user = strtolower($this->_params[0]);

        $request = $this->_guzzle->get("https://census.daybreakgames.com/s:itslittany/get/ps2:v2/character/?name.first_lower=$user&c:resolve=outfit_member_extended&c:resolve=stat_history&c:resolve=online_status&c:resolve=world&c:join=world");

        $output = json_decode($request->getBody(), true);
        $output = $output['character_list'][0]['stats']['stat_history'];

        $capture = 0;
        $defend = 0;

        for($i = 0; $i < sizeof($output); $i++)
        {
            if($output[$i]['stat_name'] == "facility_capture")
            {
                $capture = $output[$i]['all_time'];
            } else if($output[$i]['stat_name'] == "facility_defend") {
                $defend = $output[$i]['all_time'];
            }
        }

        return $this->_output->output(200, substr(($capture/$defend), 0, 4), true);
    }
}