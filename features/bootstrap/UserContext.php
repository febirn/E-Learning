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
     * @Given token with username :username
     */
    public function getToken($username)
    {
        $qb = $this->featureContext->getBuilder();
        $find = $qb->select('u_token.token')
                   ->from('user_token', 'u_token')
                   ->innerJoin('u_token', 'users', 'u', 'u_token.user_id = u.id')
                   ->where('u.username = :username')
                   ->setParameter(':username', $username)
                   ->execute()
                   ->fetch();

        $this->paramContext->token = $find['token'];
    }

    /**
     * @Then I set reset password token to :token
     */
    public function setToken($token)
    {
        $qb = $this->featureContext->getBuilder();
        $qb->update('password_reset')
           ->set('token', ':token')
           ->where('user_id', ':user_id')
           ->setParameter(':token', $token)
           ->setParameter(':user_id', $this->paramContext->id)
           ->execute();
    }

    /**
     * @When my role change to :role role
     */
    public function changeRole($role)
    {
        $qbId = $this->featureContext->getBuilder();
        $id = $qbId->select('id')
                 ->from('users')
                 ->where('id = :id')
                 ->setParameter(':id', $this->paramContext->id)
                 ->execute()
                 ->fetch()['id'];

        $qbRoleId = $this->featureContext->getBuilder();
        $roleId = $qbRoleId->select('id')
                       ->from('role')
                       ->where('name = :name')
                       ->setParameter(':name', $role)
                       ->execute()
                       ->fetch()['id'];

        $qbChangeRole = $this->featureContext->getBuilder();
        $qbChangeRole->update('user_role')
                     ->set('role_id', $roleId)
                     ->where('user_id = :user_id')
                     ->setParameter(':user_id', $id)
                     ->execute();
    }

}