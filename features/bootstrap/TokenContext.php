<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class TokenContext implements Context
{
    public $token;
    private $featureContext;

     /** @BeforeScenario */
   public function gatherContexts(BeforeScenarioScope $scope)
   {
       $environment = $scope->getEnvironment();
   
       $this->featureContext = $environment->getContext('FeatureContext');
   }

   /**
    * @Given the token :token
    */
   public function setToken($token)
   {
       $this->token = $token;
   }
}