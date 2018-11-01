<?php
/**
 * Riot Library
 *
 * Working with Twitch
 *
 * @package       API
 * @author        Marc Towler <marc@marctowler.co.uk>
 * @copyright     Copyright (c) 2018 Marc Towler
 * @license       https://github.com/Design-Develop-Realize/api/blob/master/LICENSE.md
 * @link          https://api.itslit.uk
 * @since         Version 1.1
 * @filesource
 */

namespace API\Library;

use GuzzleHttp\Client;

class Riot
{
    //Leave the base here as it will not be a bother to add the type to the call, like platform, champion etc
    const API_BASE = "https://{platform}.api.riotgames.com/lol/";

    //Cache and rate limiting
    const LONG_LIMIT_INTERVAL = 600;
    const SHORT_LIMIT_INTERVAL = 10;
    const RATE_LIMIT_LONG = 500;
    const RATE_LIMIT_SHORT = 10;
    const CACHE_LIFETIME_MINUTES = 60;

    private $_api_key;
    private $_platform;
    private $_client;
    private $_cache;

    public $shortLimitQueue;
    public $longLimitQueue;

    public function __construct(CacheInterface $cache = null)
    {
        $this->_cache    = $cache;

        $this->_client = new Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false,),));

        $tmp = new Config();
        
        $this->_api_key = $tmp->getSettings('RIOT_API_KEY');

        $this->shortLimitQueue = new \SplQueue();
        $this->longLimitQueue  = new \SplQueue();
    }

    public function get($uri, $static = false, $headers = [])
    {
        $uri = $this->format_url(self::API_BASE . $uri);

        $settings['headers'] = $headers;

        //If we haven't already, lets make sure we add our API key to the headers before we forget
        if(empty($settings['headers']['X-Riot-Token']))
        {
            $settings['headers']['X-Riot-Token'] = $this->_api_key;
        }

        //Lets see if we have the result of the call in cache
        if($this->_cache !== null && $this->_cache->has($uri))
        {
            $result = $this->_cache->get($uri);
        } else
        {
            //Lets check rate limiting calls if not a static call
            if(!$static)
            {
                $this->updateLimitQueue($this->longLimitQueue, self::LONG_LIMIT_INTERVAL, self::RATE_LIMIT_LONG);
                $this->updateLimitQueue($this->shortLimitQueue, self::SHORT_LIMIT_INTERVAL, self::RATE_LIMIT_SHORT);
            }

            //Lets call and stash the results
            $result = $this->_client->request('GET', $uri, $settings);

            //check that was actually have a good response
            if($result->getStatusCode() == 200)
            {
                //is there something in cache?
                if($this->_cache !== null)
                {
                    $this->_cache->put($uri, $result, self::CACHE_LIFETIME_MINUTES * 60);
                }
			} else {
                //Throw exception
            }
        }

        return json_decode($result->getBody(), true);
    }

    public function setPlatform($platform)
    {
        $this->_platform = $platform;
    }

    private function format_url($command)
    {
        return str_replace("{platform}", $this->_platform, $command);
    }

    /* Three possibilities here.
            1: There are timestamps outside the window of the interval,
            which means that the requests associated with them were long
            enough ago that they can be removed from the queue.
            2: There have been more calls within the previous interval
            of time than are allowed by the rate limit, in which case
            the program blocks to ensure the rate limit isn't broken.
            3: There are openings in window, more requests are allowed,
            and the program continues.*/
    private function updateLimitQueue($queue, $interval, $call_limit)
    {
        while(!$queue->isEmpty())
        {
            $timeSinceOldest = time() - $queue->bottom();

            if($timeSinceOldest > $interval)
            {
                $queue->dequeue();
            }
            elseif($queue->count() >= $call_limit)
            {
            // Check to see whether the rate limit would be broken; if so, block for the appropriate amount of time
                if($timeSinceOldest < $interval)
                {
                    //order of ops matters
                    echo("sleeping for".($interval - $timeSinceOldest + 1)." seconds\n");

                    sleep($interval - $timeSinceOldest);
                }
            } else {
                break;
            }
        }

        // Add current timestamp to back of queue; this represents the current request.
        $queue->enqueue(time());
    }

    public function get_user_id($username)
    {
        $getUser = $this->get('summoner/v3/summoners/by-name/' . $username, false);

        if(empty($getUser['id']))
        {
            //Throw new exception
        }
        
        return $getUser['id'];

    }

	public function get_champs($nameOnly = false)
	{
		//lets load the champion file, later on we will do an update check first
		$file = file_get_contents('Application/Static Files/LoL/champion.json');

		$data = json_decode($file, true);
		$data = $data['data'];

		$tmp = '';

		if($nameOnly) {
			$i = 0;

			foreach($data as $key => $value) {
				$tmp[$i] = $key;
				$i++;
			}

			$data = $tmp;
		}

		return $data;
	}

	public function get_champ_name($id)
	{
		$data = $this->get_champs();
		$output = [];

		foreach($data as $champ) {
			if($champ['key'] == $id) {
				$output = $champ['id'];
			}
		}

		return $output;
	}
}