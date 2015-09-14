<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use Lucaszz\FacebookAuthenticationBundle\Adapter\GuzzleFacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;

class GuzzleFacebookApiTest extends IntegrationTestCase
{
    /** @var GuzzleFacebookApi */
    private $adapter;
    /** @var ClientInterface */
    private $guzzleClient;
    /** @var History */
    private $history;

    /**
     * @test
     */
    public function it_requests_for_access_token()
    {
        $this->mockSuccessfulResponse();

        $accessToken = $this->adapter->accessToken('correct-code');

        $this->assertEquals('access-token', $accessToken);
        $this->assertThatRequestIsEqual($this->expectedSuccessfulAccessTokenRequest(), $this->history->getLastRequest());
    }

    /**
     * @test
     */
    public function it_can_get_me_data()
    {
        //$this->adapter->me($this->accessToken());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->adapter = $this->container->get('lucaszz_facebook_authentication.adapter.facebook_api.guzzle');
        $this->guzzleClient = $this->container->get('lucaszz_facebook_authentication.guzzle');
        $this->history = new History();

        $this->guzzleClient->getEmitter()->attach($this->history);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->adapter      = null;
        $this->guzzleClient = null;
        $this->history      = null;

        parent::tearDown();
    }

    private function mockSuccessfulResponse()
    {
        $body = array(
            "access_token" => "access-token",
            "expires" => "5179907"
        );

        $mock = new Mock();
        $mock->addResponse(new Response(200, [], Stream::factory(http_build_query($body))));

        $this->container->get('lucaszz_facebook_authentication.guzzle')->getEmitter()->attach($mock);
    }

    private function accessToken()
    {
        $dir = __DIR__.'/../accessToken';
        $files = scandir($dir, SCANDIR_SORT_DESCENDING);

        $file = $dir . '/' . $files[0];

        if($f = fopen($file, 'r')){
            $accessToken = fgets($f);
            fclose($f);

            return $accessToken;
        }

        throw new \Exception('Unable to read file with access token');
    }

    private function expectedSuccessfulAccessTokenRequest()
    {
        $request = new Request('GET', 'https://graph.facebook.com/oauth/access_token');
        $query = $request->getQuery();

        $query->set('client_id', 'xxxxxxxx');
        $query->set('redirect_uri', 'http://localhost/facebook/login');
        $query->set('client_secret', 'xxxxxxx');
        $query->set('code', 'correct-code');

        return $request;
    }

    private function assertThatRequestIsEqual(RequestInterface $expectedRequest, RequestInterface $request)
    {
        $this->assertEquals($expectedRequest->getMethod(), $request->getMethod());
        $this->assertEquals($expectedRequest->getUrl(), $request->getUrl());
    }
}
