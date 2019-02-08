<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-01-25
 * Time: 17:49
 */

namespace FitnessBundle\Service\FormError;

use Symfony\Component\Form\FormInterface;

/**
 * Interface FormErrorsServiceInterface
 * @package FitnessBundle\Service\FormError
 */
interface FormErrorServiceInterface
{
	/**
	 * @param FormInterface $form
	 * @return FormInterface
	 */
	public function checkErrors(FormInterface $form): FormInterface;
}