<?php 

use Behat\Behat\Context\Context;
use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class TokenContext implements Context
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
     * @Given token with username :username
     */
    public function getToken($username)
    {
    	$qb = $this->featureContext->getBuilder();
    	$find = $qb->select('u_token.token')
    	   		   ->from('user_token', 'u_token')
    	   		   ->innerJoin('u_token', 'users', 'u', 'u_token.user_id = u.id')
    	   		   ->where('username = :username')
    	   		   ->setParameter(':username', $username)
    	   		   ->execute()
    	   		   ->fetch();

    	$this->paramContext->token = $find['token'];
    }
}