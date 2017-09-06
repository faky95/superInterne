<?php
namespace Orange\QuickMakingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Form;
use Doctrine\ORM\Query;

class BaseController extends Controller {


	/**
	 * @param Mixed $entity
	 * @param string $formName
	 * @return \Symfony\Component\Form\Form
	 */
	protected function createCreateForm($entity, $formName, $options = array()) {
		$type = '\Orange\MainBundle\Form\\'.$formName.'Type';
		$form = $this->createForm(new $type() , $entity, $options);
		return $form;
	}
	
	/**
	 * @param \Symfony\Component\Form\Form $form
	 * @param array $fields
	 */
	protected function useFormFields($form, $fields = array()) {
		foreach($form->all() as $name => $widget) {
			if(in_array($name, $fields)==false) {
				$form->remove($name);
			}
		}
	}
	
	/**
	 * @return \Orange\MainBundle\Mapping\AbstractMapping
	 */
	public function getMapping() {
		return new \Orange\MainBundle\Mapping\AbstractMapping();
	}
	
	/**
	 * @param \Symfony\Component\Form\Form $form
	 * @param array $fields
	 */
	protected function removeFormFields($form, $fields = array()) {
		foreach($form->all() as $name => $widget) {
			if(in_array($name, $fields)==true) {
				$form->remove($name);
			}
		}
	}
	
	/**
	 * @param Request $request
	 * @param QueryBuilder $queryBuilder
	 * @param string $rendererMethod
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	protected function paginate($request, QueryBuilder $queryBuilder, $rendererMethod = 'addRowInTable', $rootColumnName = 'id') {
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');
		$numberPage = ((int)$request->query->get('iDisplayStart')/(int)$request->query->get('iDisplayLength'))+1;
		$pagination = $paginator->paginate($query, $request->query->get('page', 1), $request->query->get('iDisplayLength'));
		$this->setFilter($queryBuilder, array(), $request);
		$this->setOrder($queryBuilder, array(), $request);
		$query = $this->customResultsQuery($queryBuilder);
		$this->get('session')->set('query_client', $query->getDQL());
		$totalNumber = $this->getLengthResults($queryBuilder, $rootColumnName);
		$query->setHint('knp_paginator.count', $totalNumber);
		$pagination = $paginator->paginate($query, $numberPage, $request->query->get('iDisplayLength'), array('distinct' => false));
		$params = $pagination->getParams();
		// parameters to template
		$aaData = array();
		foreach ($pagination->getItems() as $entity) {
			$aaData[] = $this->{$rendererMethod}($entity);
		}
		$data = $this->get('session')->get('data', array());
		$data['totalNumber'] = $totalNumber;
		$this->get('session')->set('data', $data);
		$output = array(
				"sEcho" => $params['sEcho'],
				"iTotalRecords" => $totalNumber,//$pagination->getTotalItemCount(),
				"iTotalDisplayRecords" => $totalNumber,//$pagination->getTotalItemCount(),
				"aaData" => $aaData
			);
		$response = new JsonResponse($output);
		return $response;
	}
  
	/**
	 * @todo fait un tri sur le résultat
	 * @param sfWebRequest $request
	 */
	protected function setOrder(QueryBuilder $queryBuilder, $aColumns, Request $request) {
	  	if($request->query->has('iSortCol_0')) {
	  		for($i=0;$i<intval($request->query->get('iSortingCols'));$i++) {
	  			if($request->query->get('bSortable_'.intval($request->query->get('iSortCol_'.$i)))=="true" && count($aColumns)) {
	  				$queryBuilder->orderBy($aColumns[intval($request->query->get('iSortCol_'.$i))], $request->query->get('sSortDir_'.$i));
	  			}
	  		}
	  	}
	}
	
	/**
	 * @param Request $request
	 * @param array $data
	 * @param Form $form
	 */
	public function modifyRequestForForm($request, $data, $form) {
		$arrData = $request->request->all();
		foreach($data as $key => $value) {
			$arrData[$form->getName()][$key] = $value;
		}
		$request->request->replace($arrData);
		$request->setMethod('POST');
		$form->handleRequest($request);
	}
	  
	/**
	 * @todo ajoute un filtre
	 * @param sfWebRequest $request
	 */
	protected function setFilter(QueryBuilder $queryBuilder, $aColumns, Request $request) {
	  	$queryString = null;
	  	if($request->query->has('sSearch') && $request->query->get('sSearch')!="") {
	  		for($i=0 ;$i<count($aColumns);$i++) {
	  			$queryString .= ($queryString ? ' OR ' : null)."(".$aColumns[$i]." LIKE '%".trim($request->query->get('sSearch'))."%') ";
	  		}
			$queryBuilder->andWhere($queryString);
		}
	}
	
	/**
	 * @todo customize le résultat de la requête
	 * @param QueryBuilder $queryBuilder
	 */
	protected function customResultsQuery(QueryBuilder $queryBuilder) {
	  	return $queryBuilder->getQuery();
	}
	
	/**
	 * @todo retourne le nombre d'enregistrements renvoyer par le résultat de la requête
	 * @param QueryBuilder $queryBuilder
	 * @return integer
	 */
	protected function getLengthResults(QueryBuilder $queryBuilder, $rootColumnName) {
	  	$data = $queryBuilder->select(sprintf('COUNT(DISTINCT %s.%s) as number', $queryBuilder->getRootAlias(), $rootColumnName))
	  		->getQuery()->execute();
	  	return $data[0]['number'];
	}
	
	protected function getMyParameter($name, $path = array()) {
		$data = $this->container->getParameter($name);
		foreach($path as $key) {
			$data = $data[$key];
		}
		return $data;
	}
	
	/**
	 * @param Mixed $entity
	 * @param string $column
	 * @return string
	 */
	public function showEntityStatus($entity, $column) {
		$reflect = new \ReflectionClass($entity);
		$template = $this->get('twig')->loadTemplate($this->getMyParameter('template_status'));
		return $template->renderBlock('status_'.strtolower($reflect->getShortName()).'_'.$column, array(
				'entity' => $entity, 'ids' => $this->getMyParameter('ids'), 'states' => $this->getMyParameter('states')
			));
	}
}