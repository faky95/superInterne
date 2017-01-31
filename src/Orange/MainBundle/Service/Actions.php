<?php

namespace Orange\MainBundle\Service;

use Orange\MainBundle\Entity\Statut;
class Actions {
	const ACTION_TEMPLATE = '<span class="tip" ><a title="%s" href="%s"><img src="%s" /></a></span>';
	const ACTION_MODAL_TEMPLATE = '<span class="tip" ><a title="%" href="#myModal" class="actionLink" modal-url="%s" data-target="#myModal" data-toggle="modal"><img src="%s" /></a></span>';
	
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	
	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;
	
	/**
	 * @var \Orange\MainBundle\Entity\Utilisateur
	 */
	private $user;
	
	/**
	 * @var array
	 */
	private $states;
	
	/**
	 * @var string
	 */
	private $actions;
        
    /**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @param \Twig_Environment $twig        	
	 * @param \Symfony\Component\Routing\Router $router        	
	 * @param array $states
	 * @param \Symfony\Component\Security\Core\SecurityContext $security_context
	 * @param \Orange\QuickMakingBundle\Model\EntityManager $em    	
	 */
	public function __construct($twig, $router, $states, $security_context, $em) {
		$this->twig = $twig;
		$this->router = $router;
		$this->states = $states;
		$this->user = $security_context->getToken()->getUser();
        $this->em = $em;
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Action $entity        	
	 * @return string
	 */
	public function generateActionsForAction($entity) {
		$arrStatut = array(Statut::ACTION_FAIT_DELAI, Statut::ACTION_FAIT_HORS_DELAI, Statut::ACTION_SOLDEE_DELAI, Statut::ACTION_SOLDEE_DELAI);
		$actions = '<div class="btn-group">
				     <a class="btn btn-default" href="%s" title="Détails sur l\'action "><span class="icomoon-icon-eye"></span></a>';
		if($this->user->hasRole('ROLE_ADMIN') || $this->user->getId()==$entity->getAnimateur()->getId() || $this->user->hasRole('ROLE_ANIMATEUR')) {
			$actions .= '<a class="btn btn-default" href="%s" title="Modifier l\'action"><span class="icomoon-icon-pencil-3"></span></a>';
		}
		if($this->user->hasRole('ROLE_ADMIN') && !in_array($entity->getEtatCourant(), $arrStatut)) {
			$actions .= '<a class="btn btn-default actionLink" href="#myModal" modal-url="%s" data-target="#myModal" data-toggle="modal" title="Supprimer l\'action">
				<span class="icomoon-icon-remove-4"></span></a>';
			//$actions .= '<a class="btn btn-default" method="delete" href="%s" title="Supprimer l\'action"><span class="icomoon-icon-remove-4"></span></a></div>';
		}
		return sprintf($actions, $entity->getInstance()->getEspace()? $this->router->generate('details_action_espace', array('id' => $entity->getId(), 'id_espace' => $entity->getInstance()->getEspace()->getId())):
						$this->router->generate('details_action', array('id'=>$entity->getId())),
                   		$this->router->generate('edition_action', array('id'=>$entity->getId())),
						$this->router->generate('supprimer_action', array('id'=>$entity->getId()))
					);
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\ActionCyclique $entity
	 * @return string
	 */
	public function generateActionsForActionCyclique($entity) {
		$actions = '<div class="btn-group"><a class="btn btn-default" href="%s" title="Détails sur l\'action cylclique"><span class="icomoon-icon-eye"></span></a>';
		if($this->user->hasRole('ROLE_ADMIN') || $this->user->getId()==$entity->getAction()->getAnimateur()->getId() || $this->user->hasRole('ROLE_ANIMATEUR')) {
			$actions .= '<a class="btn btn-default" href="%s" title="Modifier l\'action"><span class="icomoon-icon-pencil-3"></span></a>';
		}
		return sprintf($actions, $this->router->generate('actioncyclique_show', array('id'=>$entity->getId())),
					$this->router->generate('actioncyclique_edit', array('id'=>$entity->getId()))
				);
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Bu $entity
	 * @return string
	 */
	public function generateActionsForBu($entity) {
		$test = '<div class="btn-group"><a class="btn btn-default" href="%s" title="Détails sur le Bu "><span class="icomoon-icon-eye"></span></a>';
		if($this->user->hasRole('ROLE_SUPER_ADMIN')) {
			$test = $test.'<a class="btn btn-default" href="%s" title="Modifier le Bu"><span class="icomoon-icon-pencil-3"></span></a>';
		}
		if($this->user->hasRole('ROLE_ADMIN')) {
			$test = $test.'<a class="btn btn-default" method="delete" href="%s" title="Supprimer le Bu"><span class="icomoon-icon-remove-4"></span></a></div>';
		}
		return sprintf($test, $this->router->generate('details_bu', array('id'=>$entity->getId())),
							  $this->router->generate('edition_bu', array('id'=>$entity->getId())),
							  $this->router->generate('supprimer_bu', array('id'=>$entity->getId())));
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Formule $entity
	 * @return string
	 */
	public function generateActionsForFormule($entity) {
		$test = '<div class="btn-group">';
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN')) {
			$test .= '<a class="btn btn-default actionLink" href="#myModal" modal-url="%s" data-target="#myModal" data-toggle="modal" title="Consulter la formule ">'.
				'<span class="icomoon-icon-eye"></span></a>';
		}
		if($this->user->hasRole('ROLE_ADMIN')) {
			$test = $test.'<a class="btn btn-default" method="delete" href="%s" title="Supprimer la formule"><span class="icomoon-icon-remove-4"></span></a>
											</div>';
		}
		return sprintf($test, $this->router->generate('details_formule', array('id'=>$entity->getId())),
							$this->router->generate('supprimer_formule', array('id'=>$entity->getId()))
				);
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Formule $entity
	 * @return string
	 */
	public function generateActionsForPriorite($entity) {
		$test = '<div class="btn-group">';
		$test .= '<a class="btn btn-default" href="%s" title="Supprimer la priorite"><span class="icomoon-icon-pencil-3"></span></a></div>
					  <a class="btn btn-default" method="delete" href="%s" title="Editer la priorite"><span class="icomoon-icon-remove-4"></span></a>';
		return sprintf($test, $this->router->generate('edition_priorite', array('id'=>$entity->getId())),
				$this->router->generate('supprimer_priorite', array('id'=>$entity->getId()))
				);
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Reporting $entity
	 * @return string
	 */
	public function generateActionsForReporting($entity)
	{
		$test = '<div class="btn-group">';
		$test = $test.'<a class="btn btn-default" href="%s" title="Modifier le reporting"><span class="icomoon-icon-pencil-3"></span></a>
						<a class="btn btn-default" method="delete" href="%s" title="Supprimer le reporting"><span class="icomoon-icon-remove-4"></span></a>
											</div>';
	
		return sprintf($test,$this->router->generate('edition_reporting', array('id'=>$entity->getId())),
				$this->router->generate('supprimer_reporting', array('id'=>$entity->getId()))
				);
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Structure $entity
	 * @return string
	 */
	public function generateActionsForStructure($entity) {
		$test = '<div class="btn-group">
				     <a class="btn btn-default" href="%s" title="Détails sur la structure "><span class="icomoon-icon-eye"></span></a>';
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN')) {
			$test = $test.'<a class="btn btn-default" href="%s" title="Modifier la structure"><span class="icomoon-icon-pencil-3"></span></a>';
		}
		if($this->user->hasRole('ROLE_ADMIN')) {
			$test = $test.'<a class="btn btn-default" method="delete" href="%s" title="Supprimer la structure"><span class="icomoon-icon-remove-4"></span></a>
											</div>';
		}
		return sprintf($test, $this->router->generate('details_structure', array('id'=>$entity->getId())),
				$this->router->generate('edition_structure', array('id'=>$entity->getId())),
				$this->router->generate('supprimer_structure', array('id'=>$entity->getId()))
			);
	}
	
	
	/**
	 * @param \Orange\MainBundle\Entity\Instance $entity
	 * @return string
	 */
	public function generateActionsForInstance($entity) {
		$render = '<div class="btn-group">
				     <a class="btn btn-default" href="%s" title="Détails sur l\'instance "><span class="icomoon-icon-eye"></span></a>';
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN')) {
			$render = $render.'<a class="btn btn-default" href="%s" title="Modifier l\'instance"><span class="icomoon-icon-pencil-3"></span></a>';
		}
		if(($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN')) && $entity->getAction()->Count() == 0 ) {
			$render = $render.'<a class="btn btn-default" method="delete" href="%s" title="Supprimer l\'instance "><span class="icomoon-icon-remove-4"></span></a>
											</div>';
		}
		return sprintf($render, $this->router->generate('details_instance', array('id'=>$entity->getId())),
				$this->router->generate('edition_instance', array('id'=>$entity->getId())),
				$this->router->generate('supprimer_instance', array('id'=>$entity->getId()))
			);
	}
	
	
	/**
	 * @param \Orange\MainBundle\Entity\Groupe $entity
	 * @return string
	 */
	public function generateActionsForGroupe($entity) {
		return sprintf('<div class="btn-group">
				      		<a class="btn btn-default" href="%s" title="Détails sur le groupe "><span class="icomoon-icon-eye"></span></a>
				          	<a class="btn btn-default" href="%s" title="Modifier le groupe"><span class="icomoon-icon-pencil-3"></span></a>
				          	<a class="btn btn-default" method="delete" href="%s" title="Supprimer le groupe"><span class="icomoon-icon-remove-4"></span></a>
				        </div>', 
				$this->router->generate('details_groupe', 	array('id'=>$entity->getId())),
														$this->router->generate('edition_groupe', 	array('id'=>$entity->getId())),
														$this->router->generate('supprimer_groupe', array('id'=>$entity->getId()))
		);
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Utilisateur $entity
	 * @return string
	 */
	public function generateActionsForUtilisateur($entity) {
		$actions = '<div class="btn-group">
					<a class="btn btn-default" href="%s" title="Détails sur l\'utilisateur "><span class="icomoon-icon-eye"></span></a>';
// 		<a class="btn btn-default" method="delete" href="%s" title="Transfert actions"><span class="iconic-icon-transfer"></span></a></div>
		 
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN')) {
			$actions .= '<a class="btn btn-default" href="%s" title="Modifier l\'utilisateur"><span class="icomoon-icon-pencil-3"></span></a>';
		}
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || ($this->user->hasRole('ROLE_ADMIN') && $this->user->getStructure()->getRoot()==$entity->getStructure()->getRoot())) {
			if($entity->isEnabled()){
				$actions .= '<a class="btn btn-default" method="delete" href="%s" title="Désactiver l\'utilisateur"><span class="icomoon-icon-lock-2"></span></a></div>';
			}else
				$actions .= '<a class="btn btn-default" method="delete" href="%s" title="Activer l\'utilisateur"><span class="icomoon-icon-unlocked-2"></span></a></div>';
		}
		if($this->user->hasRole('ROLE_SUPER_ADMIN')) {
			$actions .= '<a class="btn btn-default" href="%s" title="Se connecter au compte"><span class="entypo-icon-shuffle"></span></a></div>';
		}
		return sprintf($actions,
// 				$this->router->generate('transfert_action', 	array('id'=>$entity->getId())),
				$this->router->generate('details_utilisateur', 	array('id'=>$entity->getId())),
				$this->router->generate('edition_utilisateur', 	array('id'=>$entity->getId())),
				$this->router->generate('supprimer_utilisateur', array('id'=>$entity->getId())),
				$this->router->generate('dashboard').'?_want_to_be_this_user='.$entity->getUsername()
			);
	}
	
	
	/**
	 * @param \Orange\MainBundle\Entity\Domaine $entity
	 * @return string
	 */
	public function generateActionsForDomaine($entity) {
		$test = '<div class="btn-group">';
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN')) {
			$test = $test.'<a class="btn btn-default actionLink" href="#myModal" modal-url="%s" data-target="#myModal" data-toggle="modal" title="Modifier le domaine">'.
				'<span class="icomoon-icon-pencil-3"></span></a>';
		}
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN')) {
			$test = $test.'<a class="btn btn-default" method="delete" href="%s" title="Supprimer le domaine"><span class="icomoon-icon-remove-4"></span></a>
											</div>';
		}
		return sprintf($test, $this->router->generate('edition_domaine', 	array('id'=>$entity->getId())),
							  $this->router->generate('supprimer_domaine', array('id'=>$entity->getId()))
			);
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\TypeAction $entity
	 * @return string
	 */
	public function generateActionsForTypeAction($entity) {
		$render = null;
		$render = '<div class="btn-group">';
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN') ) {
			$render .= 		'<a class="btn btn-default" href="%s" title="Modifier le type"><span class="icomoon-icon-pencil-3"></span></a>
							<a class="btn btn-default" method="delete" href="%s" title="Supprimer le type"><span class="icomoon-icon-remove-4"></span></a>
						</div>';
		}
		return sprintf($render, $this->router->generate('edition_type_action', array('id'=>$entity->getId())),
				$this->router->generate('supprimer_type_action', array('id'=>$entity->getId())));
	}
	
	
	/**
	 * @param \Orange\MainBundle\Entity\Signalisation $entity
	 * @return string
	 */
	public function generateActionsForSignalisation($entity)
	{
		$test = '<div class="btn-group">
				     <a class="btn btn-default" href="%s" title="Détails sur les signalisations "><span class="icomoon-icon-eye"></span></a>';
		if($this->user->hasRole('ROLE_SUPER_ADMIN') || $this->user->hasRole('ROLE_ADMIN') || $this->user->hasRole('ROLE_ANIMATEUR') || $this->user->hasRole('ROLE_SOURCE')) {
			$test = $test.'<a class="btn btn-default" href="%s" title="Modifier les signalisations"><span class="icomoon-icon-pencil-3"></span></a>';
		}
		if( $this->user->hasRole('ROLE_ADMIN') && $entity->getAction()->count()==0) {
			$test = $test.'<a class="btn btn-default" method="delete" href="%s" title="Supprimer les signalisations"><span class="icomoon-icon-remove-4"></span></a>
											</div>';
		}
		return sprintf($test, $this->router->generate('details_signalisation', 	array('id'=>$entity->getId())),
							  $this->router->generate('edition_signalisation', 	array('id'=>$entity->getId())),
							  $this->router->generate('supprimer_signalisation', array('id'=>$entity->getId()))
	
			);
	}
	
	/**
	 * @param \Orange\MainBundle\Entity\Action $entity
	 * @return string
	 */
	public function generateActionsForSuivi($entity) {
        $libelleStatut = $this->em->getRepository('OrangeMainBundle:Statut')->findOneByCode($entity->getEtatReel());
		if($libelleStatut) {
			$actionLibelle = $libelleStatut->getLibelle();
		} else {
			$actionLibelle = 'Aucun suivi ';
		}
		return $actionLibelle;
	}
	
	/**
	 *
	 * @param \Orange\MainBundle\Entity\Espace $entity
	 * @return string
	 */
	public function generateActionsForEspace($entity){
		$test = '<div class="btn-group">
				     <a class="btn btn-default" href="%s" title="Détails sur l\'espace "><span class="icomoon-icon-eye"></span></a>';
		if($this->user->hasRole('ROLE_SUPER_ADMIN')) {
			$test = $test.'<a class="btn btn-default" href="%s" title="Modifier l\'espace"><span class="icomoon-icon-pencil-3"></span></a>';
			$test = $test.'<a class="btn btn-default" method="delete" href="%s" title="Supprimer l\'espace"><span class="icomoon-icon-remove-4"></span></a>
											</div>';
			$test = $test.'<a class="btn btn-default"  href="%s" title="Dashboard de l\'espace"><span class="icomoon-icon-screen-2"></span></a>
											</div>';
		}
		return sprintf($test, $this->router->generate('details_espace', array('id'=>$entity->getId())),
				$this->router->generate('edition_espace', array('id'=>$entity->getId())),
				$this->router->generate('supprimer_espace', array('id'=>$entity->getId())),
				$this->router->generate('dashboard_espace', array('espace_id'=>$entity->getId())));
	}
	
}
