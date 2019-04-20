<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-04-11
 * Time: 16:16
 */

namespace FitnessBundle\Service\Activity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FitnessBundle\Entity\Activity;
use FitnessBundle\Repository\ActivityRepository;

class ActivityService implements ActivityServiceInterface
{

//	/** @var Activity $activity */
//	private $activity;

	/** @var ActivityRepository $activityRepository */
	private $activityRepository;

	/**
	 * ActivityService constructor.
	 * @param ActivityRepository $activityRepository
	 */
	public function __construct(ActivityRepository $activityRepository)
	{
//		$this->activity = $activity;
		$this->activityRepository = $activityRepository;
	}


	public function saveActivity(Activity $activity): bool
	{
		return $this->activityRepository->save($activity);
	}

	public function findOneActivityById(int $id): ?Activity
	{

		return $this->activityRepository->findOneById($id);

//		return $this->activityRepository->findOneBy(['id' => $id]);
	}

	public function findAllActivities(): ?array
	{
		return $this->activityRepository->findAllActivities();
	}

	public function addClient($data): bool
	{
		return $this->activityRepository->addClient($data);
	}
}