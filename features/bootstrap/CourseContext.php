<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class CourseContext implements Context
{
	public $featureContext;
    public $paramContext;

	 /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
    
        $this->featureContext = $environment->getContext('FeatureContext');
        $this->paramContext = $environment->getContext('ParamContext');
    }

    /**
     * @When I want add admin course by username :username
     */
    public function addAdminCourse($username)
    {
    	$qbId = $this->featureContext->getBuilder();

    	$id = $qbId->select('id')
    			   ->from('users')
    			   ->where('username = :username')
    			   ->setParameter(':username', $username)
    			   ->execute()
    			   ->fetch()['id'];

    	$this->featureContext->_body['user_id'] = $id;
    }

}