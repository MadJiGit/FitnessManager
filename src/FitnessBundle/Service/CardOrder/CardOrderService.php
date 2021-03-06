<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-02-01
 * Time: 13:41
 */

namespace FitnessBundle\Service\CardOrder;

use FitnessBundle\Repository\OrdersRepository;

class CardOrderService implements CardOrderServiceInterface
{

	/** @var OrdersRepository $orderRepository */
	private $orderRepository;

	/**
	 * CardOrderService constructor.
	 * @param OrdersRepository $orderRepository
	 */
	public function __construct(OrdersRepository $orderRepository)
	{
		$this->orderRepository = $orderRepository;
	}


	/**
	 * @param $order
	 */
	public function addOrder($order)
	{
		$this->orderRepository->addOrder($order);
	}

	public function findOneOrderById($id)
	{


		return $this->orderRepository->findOneById($id);
	}

	public function editProfile($order)
	{
		// TODO: Implement editProfile() method.
	}

	public function findAllOrdersByCardId($cardId)
	{
		return $this->orderRepository->findBy(['cardId' => $cardId]);
//
//		dump($this->orderRepository->findBy(['cardId' => $cardId]));
//		exit;


	}

	public function findAllOrders()
	{
//		dump($this->orderRepository->getAllOrders());
//		exit;
		return $this->orderRepository->getAllOrders();
	}

	public function findLastOrder(int $cardId)
	{

	}
}