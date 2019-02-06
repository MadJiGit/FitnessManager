<?php
/**
 * Created by PhpStorm.
 * User: madji
 * Date: 2019-01-25
 * Time: 17:51
 */

namespace AppBundle\Service\FormError;


use Exception;
use FitnessBundle\Service\FormError\FormErrorServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ErrorMessageService
 * @package AppBundle\Service\FormError
 */
class FormErrorService implements FormErrorServiceInterface
{
	private $container;

	/**
	 * FormErrorsService constructor.
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}


	/**
	 * @param FormInterface $form
	 * @return FormInterface
	 * @throws Exception
	 */
	public function checkErrors(FormInterface $form): FormInterface
	{
		$errors = $form->getErrors(true);
		if ($errors !== null) {
			foreach ($errors as $error) {
				$this->container->get('session')->getFlashBag()->add('danger', $error->getMessage());
			}
		}

		return $form;
	}
}