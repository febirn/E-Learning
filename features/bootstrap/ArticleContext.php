<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class ArticleContext implements Context
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
}