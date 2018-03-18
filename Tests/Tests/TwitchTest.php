<?php
/**
 * Created by PhpStorm.
 * User: MarcT
 * Date: 16/03/2018
 * Time: 22:39
 */

namespace Tests;

use API\Exceptions\ClientIdRequiredException;
use API\Exceptions\UnsupportedApiVersionException;
use API\Library\Twitch;

class TwitchTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateClassWithMinimumOptions()
    {
        $Twitch = new Twitch(['client_id' => 'CLIENT-ID']);
        $this->assertInstanceOf(Twitch::class, $Twitch);
        return $Twitch;
    }

    /**
     * @depends testCanCreateClassWithMinimumOptions
     */
    public function testCanSetClientId(Twitch $Twitch)
    {
        $options = [
            'client_id' => 'TEST_CLIENT_ID',
        ];
        $Twitch->setClientId($options['client_id']);
        $this->assertEquals($Twitch->getClientId(), $options['client_id']);
    }

    public function testCreateClassWithoutClientIdThrowsException()
    {
        $this->expectException(ClientIdRequiredException::class);
        $Twitch = new Twitch([]);
    }

    public function testDefaultClassProperties()
    {
        $Twitch = new Twitch(['client_id' => 'CLIENT-ID']);
        $this->assertEmpty($Twitch->getScope());
        $this->assertNotEmpty($Twitch->getClientId());
        $this->assertEmpty($Twitch->getRedirectUri());
        $this->assertEmpty($Twitch->getClientSecret());
        $this->assertEquals($Twitch->getApiVersion(), $Twitch->getDefaultApiVersion());
    }

    public function testCanCreateClassWithValidOptions()
    {
        $options = [
            'client_id' => 'CLIENT_ID',
            'client_secret' => 'CLIENT_SECRET',
            'redirect_uri' => 'REDIRECT_URI',
            'api_version' => 3,
            'scope' => ['user_read'],
        ];
        $Twitch = new Twitch($options);
        $this->assertEquals($Twitch->getClientId(), $options['client_id']);
        $this->assertEquals($Twitch->getClientSecret(), $options['client_secret']);
        $this->assertEquals($Twitch->getRedirectUri(), $options['redirect_uri']);
        $this->assertEquals($Twitch->getApiVersion(), $options['api_version']);
        $this->assertEquals($Twitch->getScope(), $options['scope']);
    }

    /**
     * @depends testCanCreateClassWithMinimumOptions
     */
    public function testApiVersionDefaultsTo5IfNotSpecificallySet(Twitch $Twitch)
    {
        $this->assertEquals($Twitch->getApiVersion(), 5);
    }

    public function testExceptionIsThrownIfApiVersionISNotSupported()
    {
        $this->expectException(UnsupportedApiVersionException::class);
        $options = [
            'client_id' => 'CLIENT_ID',
            'api_version' => 99,
        ];
        $Twitch = new Twitch($options);
        $this->assertEquals($Twitch->getApiVersion(), 5);
    }
}