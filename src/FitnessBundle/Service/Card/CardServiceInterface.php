<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-02-01
 * Time: 12:16
 */

namespace FitnessBundle\Service\Card;


use FitnessBundle\Entity\Card;

/**
 * Interface CardServiceInterface
 * @package FitnessBundle\Service\Card
 */

interface CardServiceInterface
{
	/**
	 * @param $id
	 * @return object|null|Card
	 */
	public function findOneCardById($id);

	public function findAllCardsByUserId($id);

	public function addCard(Card $card);

	public function getNewId();
}