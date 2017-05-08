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
use Doctrine\DBAL\Driver\PDOMySql\Driver;

require_once __DIR__ . '/../../vendor/autoload.php';

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
    protected $paramContext;
    public $db;

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

        $this->paramContext = $environment->getContext('ParamContext');
    }

    public function setDb()
    {
        $setting['url'] = 'mysql://root:root@db/elearning'; 
        
        $config = new \Doctrine\DBAL\Configuration();

        $connect = \Doctrine\DBAL\DriverManager::getConnection($setting,
        $config);

        $this->db = $connect;
    }

    public function getBuilder()
    {
        $this->setDb();
        return $this->db->createQueryBuilder();
    }

    public function setOptions($query = null, $json = null)
    {
        $options = [
            'headers'   => [
                'Content-Type'  => 'application/json',
                'Authorization' => $this->paramContext->token,
            ],
            'query'     => $query,
            'json'      => $json,
        ];
        return $options;

    }

    /**
     * @When I GET url :url
     */
    public function iGetUrl($url)
    {
        $options = $this->setOptions();
        $this->_response = $this->_client->request('GET', $url, $options);
    }

    /**
     * @When I GET url :url with param:
     */
    public function iGetUrlInParam($url, TableNode $table)
    {
        foreach ($table as $key => $value) {
            $query = $value;
        }

        $options = $this->setOptions($query);

        $this->_response = $this->_client->request('GET', $url, $options);
    }

    /**
     * @When I GET url :url by column :column
     */
    public function iGetUrlByColumn($url, $column)
    {
        $column = explode(',', $column);

        foreach ($column as $key => $value) {
            $columns[$value] = $this->paramContext->{$value};
        }

        $url = $url. '/'. implode('/', $columns);

        $options = $this->setOptions();

        $this->_response = $this->_client->request('GET', $url, $options);
    }

    /**
     * @When I GET url :url by column :column and with param:
     */
    public function iGetUrlByColumnAndWithParam($url, $column, TableNode $table)
    {
        $column = explode(',', $column);

        foreach ($column as $key => $value) {
            $columns[$value] = $this->paramContext->{$value};
        }

        $url = $url. '/'. implode('/', $columns);

        foreach ($table as $key => $value) {
            $query = $value;
        }

        $options = $this->setOptions($query);

        $this->_response = $this->_client->request('GET', $url, $options);
    }

    /**
     * @When I DELETE url :url
     */
    public function iDeleteUrl($url)
    {
        $options = $this->setOptions();
        $this->_response = $this->_client->request('DELETE', $url, $options);
    }

    /**
     * @When I DELETE url :url with param:
     */
    public function iDeleteUrlInParam($url, TableNode $table)
    {
        foreach ($table as $key => $value) {
            $query = $value;
        }

        $options = $this->setOptions($query);

        $this->_response = $this->_client->request('DELETE', $url, $options);
    }

    /**
     * @When I DELETE url :url by column :column
     */
    public function iDeleteUrlByColumn($url, $column)
    {
        $column = explode(',', $column);

        foreach ($column as $key => $value) {
            $columns[$value] = $this->paramContext->{$value};
        }

        $url = $url. '/'. implode('/', $columns);

        $options = $this->setOptions();

        $this->_response = $this->_client->request('DELETE', $url, $options);
    }

    /**
     * @When I DELETE url :url by column :column and with param:
     */
    public function iDeleteUrlByColumnAndWithParam($url, $column, TableNode $table)
    {
        $column = explode(',', $column);

        foreach ($column as $key => $value) {
            $columns[$value] = $this->paramContext->{$value};
        }

        $url = $url. '/'. implode('/', $columns);

        foreach ($table as $key => $value) {
            $query = $value;
        }

        $options = $this->setOptions($query);

        $this->_response = $this->_client->request('DELETE', $url, $options);
    }

    public function setRequest($method, $url, $options = null)
    {
        $this->_request = [
            'method'    => $method,
            'url'       => $url,
            'options'   => $options,
        ];
    }

    /**
     * @When I POST url :url
     */
    public function iPostUrl($url)
    {
        $options = $this->setOptions();
        return $this->setRequest('POST', $url, $options);
    }

    /**
     * @When I POST url :url with param:
     */
    public function iPostUrlWithParam($url, TableNode $table = null)
    {
        foreach ($table as $key => $value) {
            $query = $value;
        }

        $options = $this->setOptions($query);
        
        return $this->setRequest('POST', $url, $options);
    }

    /**
     * @When I POST url :url by column :column
     */
    public function iPostUrlByColumn($url, $column)
    {
        $column = explode(',', $column);

        foreach ($column as $key => $value) {
            $columns[$value] = $this->paramContext->{$value};
        }

        $url = $url. '/'. implode('/', $columns);

        $options = $this->setOptions();

        return $this->setRequest('POST', $url, $options);
    }

    /**
     * @When I POST url :url by column :column and with param:
     */
    public function iPostUrlByColumnAndWithParam($url, $column, TableNode $table)
    {
        $column = explode(',', $column);

        foreach ($column as $key => $value) {
            $columns[$value] = $this->paramContext->{$value};
        }

        $url = $url. '/'. implode('/', $columns);

        foreach ($table as $key => $value) {
            $query = $value;
        }

        $options = $this->setOptions($query);
        return $this->setRequest('POST', $url, $options);
    }

    /**
     * @When I PUT url :url
     */
    public function iPutUrl($url)
    {
        $options = $this->setOptions();
        return $this->setRequest('PUT', $url, $options);
    }

    /**
     * @When I PUT url :url with param:
     */
    public function iPutUrlWithParam($url, TableNode $table = null)
    {
        foreach ($table as $key => $value) {
            $query = $value;
        }

        $options = $this->setOptions($query);
        
        return $this->setRequest('PUT', $url, $options);
    }

    /**
     * @When I PUT url :url by column :column
     */
    public function iPutUrlByColumn($url, $column)
    {
        $column = explode(',', $column);

        foreach ($column as $key => $value) {
            $columns[$value] = $this->paramContext->{$value};
        }

        $url = $url. '/'. implode('/', $columns);

        $options = $this->setOptions();

        return $this->setRequest('PUT', $url, $options);
    }

    /**
     * @When I PUT url :url by column :column and with param:
     */
    public function iPutUrlByColumnAndWithParam($url, $column, TableNode $table)
    {
        $column = explode(',', $column);

        foreach ($column as $key => $value) {
            $columns[$value] = $this->paramContext->{$value};
        }

        $url = $url. '/'. implode('/', $columns);

        foreach ($table as $key => $value) {
            $query = $value;
        }

        $options = $this->setOptions($query);
        return $this->setRequest('PUT', $url, $options);
    }

    /**
     * @When I fill :name with :value
     */
    public function iFillWith($name, $value)
    {
        if ($value == 'random_username') {
            $value = md5(openssl_random_pseudo_bytes(12));
        } elseif ($value == 'random_email') {
            $value = md5(openssl_random_pseudo_bytes(12)). '@gmail.com';
        }
        $this->_body[$name] = $value;
    }
    /**
     * @Then I store it
     */
    public function iStoreIt()
    {
        $query = $this->_request['options']['query'];
        $json = $this->_body;
        $options = $this->setOptions($query, $json);

        try {

            $token = $this->paramContext->token;

            $this->_response = $this->_client
                                    ->request($this->_request['method'], $this->_request['url'], $options);
        } catch (Exception $exception) {
            $this->getException($exception);
        }
    }

    /**
     * @Then I see the result
     */
    public function iSeeTheResult()
    {
        echo $this->_response->getBody();
    }

    /**
     * @getException Error
     */
    public function getException($exception)
    {
        $getResponse = $exception->getResponse();

        $data = json_decode($getResponse->getBody()->getContents());
 
        if (!($data->status == 200)) {
            if (!empty($data->data)) {
                throw new Exception($data->data);
            } else {
                throw new Exception($data);
            }
        }
    }
}