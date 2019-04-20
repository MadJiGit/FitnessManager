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

interface ActivityServiceInterface
{

	public function saveActivity(Activity $activity): bool;

	public function findOneActivityById(int $id): ?Activity;

	public function findAllActivities(): ?array;

	public function addClient($data): bool;
}