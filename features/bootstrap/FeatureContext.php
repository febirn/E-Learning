<?php

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{

    private $_client;
    private $_parameters = [];
    private $_request;
    protected $_response;
    public $_body;
    protected $tokenContext;
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(array $parameters)
    {
        $this->_parameters = $parameters;
        $this->_client = new Client(['base_uri' => $this->_parameters['base_url']]);
    }

     /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
    
        $this->tokenContext = $environment->getContext('TokenContext');
    }

    /**
     * @When I GET url :url
     */
    public function iGetUrl($url)
    {
        $headers = [
            'Content-type'  => 'application/json',
            'Authorization' => $this->tokenContext->token,
        ];

        $this->_response = $this->_client->request('GET', $url, ['headers' => $headers]);
    }

    /**
     * @When I GET url :url in page :page
     */
    public function iGetUrlInPage($url, $page)
    {
        $headers = [
            'Content-type'  => 'application/json',
            'Authorization' => $this->tokenContext->token,
        ];

        $query = [
            'page'  => $page,
        ];

        $options = [
            'headers'   => $headers,
            'query'     => $query,
        ];

        $this->_response = $this->_client->request('GET', $url, $options);
    }


    /**
     * @When I POST url :url
     */
    public function iPostUrl($url)
    {
        $this->_request = [
            'method'=> 'POST',
            'url'   => $url,
        ];
    }

    /**
     * @When I PUT url :url with id :id
     */
    public function iPutUrl($url, $id)
    {
        $this->_request = [
            'method'=> 'PUT',
            'url'   => $url.'/'.$id,
        ];
    }

    /**
     * @When I Delete url :url with id :id
     */
    public function iDeleteUrl($url, $id)
    {
        $headers = [
            'Content-type'  => 'application/json',
            'Authorization' => $this->tokenContext->token,
        ];

        $this->_response = $this->_client->request('DELETE', $url.'/'.$id, ['headers' => $headers]);
    }

    /**
     * @When I fill :name with :value
     */
    public function iFillWith($name, $value)
    {
        $this->_body[$name] = $value;
    }

    /**
     * @Then I store it
     */
    public function iStoreIt()
    {
        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => $this->tokenContext->token,
        ];
        $body = json_encode($this->_body);

        $this->_response = $this->_client
                                ->request($this->_request['method'], $this->_request['url'], ['headers' => $headers, 'json' => $this->_body]);
    }

    /**
     * @Then I see the result
     */
    public function iSeeTheResult()
    {
        echo $this->_response->getBody();
    }

    /**
     * @When I GET url :url by :param with :value
     */
    public function getBy($url, $param, $value)
    {
        $headers = [
            'Content-type'  => 'application/json',
            'Authorization' => $this->tokenContext->token,
        ];

        $query = [
            $param  => $value,
        ];

        $options = [
            'headers'   => $headers,
            'query'     => $query,
        ];

        $this->_response = $this->_client->request('GET', $url, $options);
    }
}