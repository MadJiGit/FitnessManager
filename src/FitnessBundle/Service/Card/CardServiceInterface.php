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
	public function findOneCardById(int $id);

	public function selectByIdAsc(int $id);

	public function getNewId();

	public function findAllCardsByUserId(int $id);

	public function findAllCards();

	public function saveCard(Card $card);

	public function isVisitPossible(int $cardId);

}