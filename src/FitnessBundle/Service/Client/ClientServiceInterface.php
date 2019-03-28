<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-27
 * Time: 23:06
 */

namespace FitnessBundle\Service\Client;


interface ClientServiceInterface
{

	public function findCardByNumber(string $searchedNumber);

	public function findCardByUsername($searchedUsername);

	public function findCardByEmail($searchedEmail);

	public function findCardOwnerByUsername($searchedUsername);

	public function findCardOwnerByEmail($searchedEmail);
}