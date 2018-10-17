<?php
namespace Orange\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Orange\QuickMakingBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Orange\MainBundle\Entity\Action;
use Orange\MainBundle\Entity\Statut;
use Orange\MainBundle\Utils\ActionUtils;
use Orange\MainBundle\OrangeMainEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Orange\MainBundle\Form\ActionType;
use Orange\MainBundle\OrangeMainForms;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\Response;
use Orange\MainBundle\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Request;

class ApplicationController extends BaseController
{
	
	/**
	 * @Route("soap", name="soap")
	 */
	public function indexAction() {
		$soapClient = new \SoapClient($this->getParameter('wsdl_http').'/ws/super?wsdl', array('trace'=>1, 'exceptions'=>1, 'cache_wsdl'=>WSDL_CACHE_NONE));
		$form = $this->createForm(new ActionType(OrangeMainForms::ACTION_BU), new Action());
		$object = new \Orange\MainBundle\Binding\ActionBinding();
		try{
			$data = $this->getRequest()->request->all();
			foreach ($data[$form->getName()] as $key => $value) {
				$object->{$key} = $value;
			}
			//echo($xml->asXML());exit;
			$soapClient->createAction($object, 'antadiop.sarr@orange-sonatel.com', 'RADAR');
			return new JsonResponse(json_decode($soapClient->createAction($data, 'antadiop.sarr@orange-sonatel.com', 'RADAR')));
		} catch(\SoapFault $fault){
			// <xmp> tag displays xml output in html
			exit($soapClient->__getLastResponse());
			exit('Request : <br/><xmp>'.$soapClient->__getLastRequest().'</xmp><br/><br/> Error Message : <br/>'.$fault->getMessage());
		}
	}
	
	/**
	 * @Route ("/list_instance", name="list_instance")
	 */
	public function listInstanceByApplicationAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$params = $request->request->all();
		var_dump($params['application']);
		$arrData = $em->getRepository('OrangeMainBundle:Instance')->listByApplication($params['application'], $params['email']);
		$output = $arrData == null ? array(0 => array('id' => null, 'libelle' => 'Choisir une instance  ...')) : array();
		foreach ($arrData as $data) {
			$output[] = array('id' => $data['id'], 'libelle' => $data['libelle']);
		}
		//var_dump($output);
		return new JsonResponse($output);
	}
	
	/**
	 * @Route ("/list_domaine", name="list_domaine")
	 */
	public function listDomaineByInstanceAction(Request $request) {
		return $this->forward('OrangeMainBundle:Action:listDomaineByInstance', array(), array(), array('id' => $request->request->get('instance')));
	}
	
	/**
	 * @Route ("/list_typeaction", name="list_typeaction")
	 */
	public function listTypeActionByInstanceAction(Request $request) {
		return $this->forward('OrangeMainBundle:Action:listTypeByInstance', array(), array(), array('id' => $request->request->get('instance')));
	}
	
	/**
	 * @Route ("/list_user", name="list_user")
	 */
	public function listUserByInstanceAction(Request $request) {
		return $this->forward('OrangeMainBundle:Action:listPorteurByInstance', array(), array(), array('id' => $request->request->get('instance')));
	}
	
	/**
	 * @Route ("/create_action", name="create_action")
	 */
	public function createactionAction(Request $request) {
		$data = $dataRequest = $request->request->all();
		unset($dataRequest['animateur'], $dataRequest['application']);
		$em = $this->getDoctrine()->getManager();
		$dispatcher = $this->get('event_dispatcher');
		$dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent(
				$em->getRepository('OrangeMainBundle:Utilisateur')->findOneByEmail($data['animateur']), $request, new Response()
			));
		$entity = new Action();
		$user = $em->getRepository('OrangeMainBundle:Utilisateur')->findOneByEmail($data['animateur']);
		$entity->setAnimateur($user);
		$form = $this->createCreateForm($entity, 'Action', array('csrf_protection'=>false, 'attr'=>array('espace_id' => null, 'chantier_id' => null)));
		$this->setRequestForForm($request, $dataRequest, $form);
		$entity->setStructure($entity->getPorteur()->getStructure());
		if($form->isValid()) {
			$entity->setAnimateur($this->getUser());
			$em->persist($entity);
			$em->flush();
			$entity->setEtatCourant(Statut::ACTION_NOUVELLE);
			$entity->setEtatReel(Statut::ACTION_NOUVELLE);
			ActionUtils::setReferenceAction($em, $entity);
			ActionUtils::changeStatutAction($em, $entity, Statut::ACTION_NOUVELLE, $user, sprintf(
					"Nouvelle action créée par %s depuis %s", $user->getNomComplet(), $data['application']
				));
			$event = $this->get('orange_main.action_event')->createForAction($entity);
			$dispatcher->dispatch(OrangeMainEvents::ACTION_CREATE_NOUVELLE, $event);
			$em->flush();
			$output = array('success' => true, 'data' => array('id' => $entity->getId(), 'reference' => $entity->getReference()));
		} else {
			$output = array('success' => false);
		}
		return new JsonResponse($output);
	}
}
