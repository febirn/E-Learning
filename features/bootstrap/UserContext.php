<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class UserContext implements Context
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
     * @When I verify user with email :email
     */
    public function verifyEmail($email)
    {
    	$qb = $this->featureContext->getBuilder();
    	$qb->update('users')
    	   ->set('is_active', 1)
    	   ->where("email = :email")
    	   ->setParameter(':email', $email)
    	   ->execute();
    }

    /**
     * @When I delete user with username :username
     */
    public function deleteUser($username)
    {
    	$qb = $this->featureContext->getBuilder();
    	$qb->delete('users')
    	   ->where("username = :username")
    	   ->setParameter(':username', $username)
    	   ->execute();
    }

    /**
     * @Given information about user with username :username
     */
    public function getUser($username)
    {
        $qb = $this->featureContext->getBuilder();
        $find = $qb->select('*')
                   ->from('users')
                   ->where('username = :username')
                   ->setParameter(':username', $username)
                   ->execute()
                   ->fetch();

        foreach ($find as $key => $value) {
            $this->paramContext->{$key} = $value;
        }

    }
}