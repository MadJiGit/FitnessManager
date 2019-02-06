<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-02-06
 * Time: 15:11
 */

namespace FitnessBundle\Service\FormError;

use Symfony\Component\Form\FormInterface;

/**
 * Interface FormErrorsServiceInterface
 * @package AppBundle\Service\FormError
 */
interface FormErrorServiceInterface
{
	/**
	 * @param FormInterface $form
	 * @return FormInterface
	 */
	public function checkErrors(FormInterface $form): FormInterface;
}