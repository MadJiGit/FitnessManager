<?php

namespace FitnessBundle\Controller;

use FitnessBundle\Entity\Article;
use FitnessBundle\Entity\User;
use FitnessBundle\Form\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 * @Route("/article/create", name="article_create")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 *
	 */
	public function create(Request $request)
	{
		$article = new Article();
		$form = $this->createForm(ArticleType::class, $article);
		$form->handleRequest($request);
		$user = $this->getUser();

		if ($form->isSubmitted() && $form->isValid()) {
			$article->setAuthor($this->getUser());
			$em = $this->getDoctrine()->getManager();
			$em->persist($article);
			$em->flush();

			// must change to article_view
//			return $this->redirectToRoute('user_profile');
			return $this->viewAll();
		}

		return $this->render('article/create.html.twig',
			array('form' => $form->createView(),
				'user' => $user));
	}


	////	 * @return \Symfony\Component\HttpFoundation\Response

	/**
	 * @Route("/viewAll", name="article_view_all")
	 */
	public function viewAll()
	{
		$user = $this->getUser();
		$articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
		return $this->render('article/viewAll.html.twig',
			array('articles' => $articles, 'user' => $user));
	}

	/**
	 * @Route("/viewOne/{id}", name="article_view_one")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function viewOne($id)
	{
		$user = $this->getUser();
		$article = $this->getDoctrine()->getRepository(Article::class)->find($id);
		return $this->render('article/viewOne.html.twig',
			array('article' => $article, 'user' => $user));
	}

	/**
	 * @Route("/article/edit/{id}", name="article_edit")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function editArticle($id, Request $request)
	{
		$article = $this
			->getDoctrine()
			->getRepository(Article::class)
			->find($id);

		if ($article === null) {
			return $this->redirectToRoute('article_view_one');
		}

		$form = $this->createForm(ArticleType::class, $article);

		$form->handleRequest($request);

		/** @var User $user */
		$user = $this->getUser();

//		dump($article->getId());
//		exit;

		if ($user->isAuthor($article) || $user->isSuperAdmin() || $user->isAdmin()) {

			if ($form->isValid() && $form->isSubmitted()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($article);
				$em->flush();

				//TODO дава грешка с id при едит на test

				return $this->redirectToRoute('article_view_one',
					array('id' => $article->getId(), 'user' => $user));

			}

			$message = ('Mr/s, ' . $user->getFullName() . '! You have no rights to edit this article');

			return $this->redirectToRoute('article_view_one',
				array('article' => $article,
					'form' => $form->createView(),
					'user' => $user,
					'message' => $message)
			);

		}

		return $this->render('article/edit.html.twig',
			array('article' => $article,
				'form' => $form->createView(),
				'user' => $user)
		);
	}

	/**
	 * @Route("/article/delete/{id}", name="article_delete")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
	 * @param $id
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */

	public function deleteArticle($id, Request $request)
	{
		$article = $this->getDoctrine()->getRepository(Article::class)->find($id);

		if ($article === null) {
			return $this->redirectToRoute('article_view_one');
		}

		$form = $this->createForm(ArticleType::class, $article);
		$form->handleRequest($request);

		$user = $this->getUser();

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($article);
			$em->flush();

			return $this->redirectToRoute('article_view_all', array('user' => $user));
		}

		return $this->render('article/delete.html.twig',
			array('article' => $article,
				'form' => $form->createView(),
				'user' => $user));
	}
}
