<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-02-01
 * Time: 13:31
 */

namespace FitnessBundle\Service\CardOrder;


interface CardOrderServiceInterface
{

	public function addOrder($order);

	public function findOneOrderById($id);

	public function editProfile($order);

	public function findAllOrdersByCardId($cardId);

	public function findAllOrders();

	public function findLastOrder(int $cardId);

}