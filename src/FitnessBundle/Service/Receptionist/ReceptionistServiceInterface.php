<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-03-27
 * Time: 23:06
 */

namespace FitnessBundle\Service\Receptionist;


interface ReceptionistServiceInterface
{

	public function findCardByNumber(string $searchedNumber);

	public function findCardByUsername(string $searchedUsername);

	public function findCardByEmail(string $searchedEmail);

	public function findIdCardOwnerByUsername(string $searchedUsername);

	public function findIdCardOwnerByEmail(string $searchedEmail);

	public function findIdCardOwnerByCardNumber(string $searchedNumber);

	public function findUserById(int $id);
}