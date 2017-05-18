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
            'query' => $query,
            'json'  => $json,
        ];
        return $options;

    }

    /**
     * @When /^I "(?<method>[^"]*)" in "(?<url>[^"]*)"?(?:| by column "(?<column>[^"]*)")?(?:| and)?(?:| with param:)$/
     */
    public function iToUrl($method, $url, $column = null, TableNode $param = null)
    {
        if ($method == "GET" || $method == "DELETE") {
            $this->iShowData($method, $url, $column, $param);
        } elseif ($method == "POST" || $method == "PUT") {
            $this->iStoreData($method, $url, $column, $param);
        }
    }

    public function iShowData($method, $url, $column = null ,TableNode $param = null)
    {
        if ($column != null) {
            $url = $this->setArgument($url, $column);
        }

        if ($param !== null) {
            $query = $this->setQuery($param);
            $options = $this->setOptions($query);
        } else {
            $options = $this->setOptions();
        }

        $this->_response = $this->_client->request($method, $url, $options);

    }

    public function iStoreData($method, $url, $column = null, TableNode $param = null)
    {
        if ($column !== null) {
            $url = $this->setArgument($url, $column);
        }

        if ($param !== null) {
            $query = $this->setQuery($param);
            $options = $this->setOptions($query);
        } else {
            $options = $this->setOptions();
        }

        return $this->setRequest($method, $url, $options);
    }

    public function setArgument($url, $args)
    {
        $link = explode('/', $url);
        
        if (count($link) > 1) {
            $unlink = array_pop($link);
        }
        
        $column = explode(',', $args);

        foreach ($column as $key => $value) {
            array_push($link, $this->paramContext->{$value});
        }

        if (count($link) > 2 && $unlink != null) {
            array_push($link, $unlink);
        }

        return implode('/',$link);
    }

    public function setQuery(TableNode $query)
    {
        foreach ($query as $key => $value) {
            $param = $value;
        }
        return $param;
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
     * @When I fill post with this:
     */
    public function iFillWith(TableNode $table)
    {
        $t = $table->getHash();

        foreach ($t as $keyT => $valueT) {
            foreach ($valueT as $keyValueT => $valueValueT) {
                if ($valueValueT != "") {
                    $this->_body[$keyValueT][] = $valueValueT;
                }
            }
        }       
        
        foreach ($this->_body as $key => $value) {
            if (count($this->_body[$key]) < 2) {
                unset($this->_body[$key][0]);
                $this->_body[$key] = $value[0];
            }
        }
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
            $this->_response = $this->_client
                                    ->request($this->_request['method'], $this->_request['url'], $options);
        } catch (Exception $exception) {
            $this->getException($exception);
        }
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

    /**
     * @Given information about :table by :column :value 
     */
    public function getData($table, $column, $value)
    {
        $qb = $this->getBuilder();

        $result = $qb->select('*')
                     ->from($table)
                     ->where($column. ' = :'.$column)
                     ->setParameter(':'.$column, $value)
                     ->execute()
                     ->fetch();

        foreach ($result as $key => $value) {
            $this->paramContext->{$key} = $value;
        }
    }
}