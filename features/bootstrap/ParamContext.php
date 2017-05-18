<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class ParamContext implements Context
{
	 /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
    
        $this->featureContext = $environment->getContext('FeatureContext');
    }

}