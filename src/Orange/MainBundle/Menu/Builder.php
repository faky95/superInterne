<?php
namespace Orange\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Knp\Menu\MenuItem;
use Orange\MainBundle\Entity\Utilisateur;
use Symfony\Component\Routing\RequestContext;
use Orange\MainBundle\Entity\Config;

class Builder extends ContainerAware
{
    public function main(FactoryInterface $factory, array $options)
    {
       	$menu = $factory->createItem('MainMenu');
    	$menu->addChild('Accueil', array('route' => 'dashboard', 'label' => 'Accueil', 'attributes' => array('class' => 'icomoon-icon-home')));
       	$user = $this->container->get('security.context')->getToken()->getUser();
       	
   		//$data = $this->simpleMenu($menu,$user);
       	if($user->hasRole($user::ROLE_SUPER_ADMIN)) {
       		$this->superAdminMenu($menu);
       	} else {
	       	if($user->hasRole($user::ROLE_ADMIN)) {
	       		$this->adminMenu($menu);
	       	}
	       	if($user->hasRole($user::ROLE_ADMIN && $user::ROLE_MANAGER && $user::ROLE_ANIMATEUR)) {
	       		$this->adminManagerAnimateurMenu($menu);
	       	}
	       	if($user->hasRole($user::ROLE_ADMIN && $user::ROLE_MANAGER)) {
	       		$this->adminManagerMenu($menu);
	       	}
	       	if($user->hasRole($user::ROLE_ADMIN && $user::ROLE_MANAGER)) {
	       		$this->adminAnimateurMenu($menu);
	       	}
	       	if($user->hasRole($user::ROLE_MANAGER)) {
	       		$this->managerMenu($menu, $user);
	       	}
	       	if($user->hasRole($user::ROLE_ANIMATEUR_ONLY)) {
	       		$this->animateurMenu($menu, $user);
	       	}
	       	if($user->hasRole($user::ROLE_PORTEUR)) {
	       		$this->simpleMenu($menu, $user);
	       	}
	       	if($user->hasRole($user::ROLE_SOURCE)) {
	       		$this->sourceMenu($menu);
	       	} 
	       	if($user->hasRole($user::ROLE_RAPPORTEUR)) {
	       		$this->rapporteurMenu($menu, $user);
	       	}
	       	if($user->hasRole($user::ROLE_MEMBRE_ESPACE)) {
	       		$this->membreEspaceMenu($menu, $user);
	       	}
	       	
       	}
       	return $menu;
       	
    }
    
    
    public function adminAnimateurMenu($menu)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$menu->addChild('Action', array('route' => 'les_actions', 'label' => 'Actions', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
	   	$menu->addChild('BU', array('route' => 'les_bu', 'label' => 'BU', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
    	$menu->addChild('Structure', array('route' => 'les_structures', 'label' => 'Structure', 'attributes' => array('class' => 'icomoon-icon-grid')));
    	$menu->addChild('Utilisateur', array('route' => 'les_utilisateurs', 'label' => 'Utilisateurs', 'attributes' => array('class' => 'entypo-icon-users')));
    	$menu->addChild('Domaine', array('route' => 'les_domaine', 'label' => 'Domaine', 'attributes' => array('class' => 'icomoon-icon-earth')));
    	$menu->addChild('TypeAction', array('route' => 'les_types_action', 'label' => 'Type Action', 'attributes' => array('class' => 'brocco-icon-type')));
    	$menu->addChild('Instance', array('route' => 'les_instance', 'label' => 'Instances', 'attributes' => array('class' => 'icomoon-icon-office')));
    	$menu->addChild('Formule', array('route' => 'les_formules', 'label' => 'Formules', 'attributes' => array('class' => 'silk-icon-plus')));
		if($user->getStructure()->getBuPrincipal()->getSignalisation() == 1){
			$menu->addChild('Signalisation', array('route' => 'les_signalisations', 'Label' => 'Signalisation', 'attributes' => array('class' => 'entypo-icon-warning')));
		}
    	$menu->addChild('Reporting', array('route' => 'les_reportings', 'label' => 'Reporting', 'attributes' => array('class' => 'icomoon-icon-rotate-2')));
    
    	$menu['Action']->addChild('ajout_action', array('route' =>'nouvelle_action', 'label' => 'Ajouter une action', 'attributes' => array('class' => 'icomoon-icon-plus')));
    	$menu['Action']->addChild('liste_action', array('route' =>'les_actions', 'label' => 'Liste des actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('mes_action', array('route' =>'mes_actions', 'label' => 'Mes actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('importer_action', array('route' =>'chargement_action', 'label' => 'Importer des actions', 'attributes' => array('class' => 'icomoon-icon-download')));
    //	$menu['Action']->addChild('action_perso', array('uri' =>'#', 'label' => 'Mes actions perso', 'attributes' => array('class' => 'icomoon-icon-user')));
    	$menu['Action']->addChild('action_cyclique', array('route' =>'actioncyclique', 'label' => 'Action cyclique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	if($user->getStructure()->getBuPrincipal()->hasconfig(Config::BU_ACTION_GENERIQUE)==true)
    		$menu['Action']->addChild('action_generique', array('route' =>'les_actiongeneriques', 'label' => 'Action générique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	$menu['Action']->setChildrenAttribute('class', 'sub');
    
    	$menu['Instance']->addChild('ajout_instance', array('route' => 'nouvelle_instance', 'label' => 'Ajouter une instance', 'attributes' => array('class' => 'icomoon-icon-plus')));
    	$menu['Instance']->addChild('import_instance', array('uri' =>'#', 'label' => 'Importer des instances', 'attributes' => array('class' => 'icomoon-icon-download')));
    	$menu['Instance']->addChild('list_instance', array('route' => 'les_instance', 'label' => 'Liste des instances', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Instance']->setChildrenAttribute('class', 'sub');
    
    	$menu['Utilisateur']->addChild('ajout_utilisateur', array('route' =>'fos_user_registration_register', 'label' => 'Ajouter un utilisateur', 'attributes' => array('class' => 'icomoon-icon-user-plus-2')));
    	$menu['Utilisateur']->addChild('import_utilisateur', array('route' =>'chargement_utilisateur', 'label' => 'Importer des utilisateurs', 'attributes' => array('class' => 'icomoon-icon-download')));
    	$menu['Utilisateur']->addChild('list_utilisateur', array('route' =>'les_utilisateurs', 'label' => 'Liste des utilisateurs', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Utilisateur']->addChild('list_groupe', array('route' =>'les_groupe', 'label' => 'Liste des groupes', 'attributes' => array('class' => 'icomoon-icon-people')));
    	$menu['Utilisateur']->setChildrenAttribute('class', 'sub');
    
    	return $menu;
    }
    
    /**
     * @param MenuItem $menu
     * @return MenuItem
     */
    public function superAdminMenu($menu)
	{
		$menu->addChild('Action', array('route' => 'les_actions', 'label' => 'Actions', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
        $menu->addChild('BU', array('route' => 'les_bu', 'label' => 'BU', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
        $menu->addChild('Espace', array('route' => 'les_espaces', 'label' => 'Espaces', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
        $menu->addChild('Projet', array('route' => 'les_projets', 'label' => 'Projets', 'attributes' => array('class' => ' icomoon-icon-target')));
        $menu->addChild('Structure', array('route' => 'les_structures', 'label' => 'Structure', 'attributes' => array('class' => 'icomoon-icon-grid')));
        $menu->addChild('Utilisateur', array('route' => 'les_utilisateurs', 'label' => 'Utilisateurs', 'attributes' => array('class' => 'entypo-icon-users')));
        $menu->addChild('Domaine', array('route' => 'les_domaine', 'label' => 'Domaine', 'attributes' => array('class' => 'icomoon-icon-earth')));
        $menu->addChild('TypeAction', array('route' => 'les_types_action', 'label' => 'Type Action', 'attributes' => array('class' => 'brocco-icon-type')));
        $menu->addChild('Instance', array('route' => 'les_instance', 'label' => 'Instances', 'attributes' => array('class' => 'icomoon-icon-office')));
		$menu->addChild('Formule', array('route' => 'les_formules', 'label' => 'Formules', 'attributes' => array('class' => 'silk-icon-plus')));
		$menu->addChild('Signalisation', array('route' => 'les_signalisations', 'Label' => 'Signalisation', 'attributes' => array('class' => 'entypo-icon-warning')));
        $menu->addChild('Reporting', array('route' => 'les_reportings', 'label' => 'Reporting', 'attributes' => array('class' => 'icomoon-icon-rotate-2')));
        
        $menu['Action']->addChild('ajout_action', array('route' =>'nouvelle_action', 'label' => 'Ajouter une action', 'attributes' => array('class' => 'icomoon-icon-plus')));
        $menu['Action']->addChild('liste_action', array('route' =>'les_actions', 'label' => 'Liste des actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('mes_action', array('route' =>'mes_actions', 'label' => 'Mes actions', 'attributes' => array('class' => 'icomoon-icon-list')));
        $menu['Action']->addChild('importer_action', array('route' =>'chargement_action', 'label' => 'Importer des actions', 'attributes' => array('class' => 'icomoon-icon-download')));
        //$menu['Action']->addChild('action_perso', array('uri' =>'#', 'label' => 'Mes actions perso', 'attributes' => array('class' => 'icomoon-icon-user')));
        $menu['Action']->addChild('action_cyclique', array('route' =>'actioncyclique', 'label' => 'Action cyclique', 'attributes' => array('class' => 'cut-icon-reload ')));
        $menu['Action']->addChild('action_generique', array('route' =>'les_actiongeneriques', 'label' => 'Action générique', 'attributes' => array('class' => 'cut-icon-reload ')));
        $menu['Action']->setChildrenAttribute('class', 'sub');

		$menu['Structure']->addChild('liste_structure', array('route' =>'les_structures', 'label' => 'Liste des structures', 'attributes' => array('class' => 'icomoon-icon-list')));
		$menu['Structure']->setChildrenAttribute('class', 'sub');

        $menu['Instance']->addChild('ajout_instance', array('route' => 'nouvelle_instance', 'label' => 'Ajouter une instance', 'attributes' => array('class' => 'icomoon-icon-plus')));
        $menu['Instance']->addChild('import_instance', array('uri' =>'#', 'label' => 'Importer des instances', 'attributes' => array('class' => 'icomoon-icon-download')));
        $menu['Instance']->addChild('list_instance', array('route' => 'les_instance', 'label' => 'Liste des instances', 'attributes' => array('class' => 'icomoon-icon-list')));
        $menu['Instance']->setChildrenAttribute('class', 'sub');
		
        $menu['Utilisateur']->addChild('ajout_utilisateur', array('route' =>'fos_user_registration_register', 'label' => 'Ajouter un utilisateur', 'attributes' => array('class' => 'icomoon-icon-user-plus-2')));
    	$menu['Utilisateur']->addChild('import_utilisateur', array('route' =>'chargement_utilisateur', 'label' => 'Importer des utilisateurs', 'attributes' => array('class' => 'icomoon-icon-download')));
    	$menu['Utilisateur']->addChild('list_utilisateur', array('route' =>'les_utilisateurs', 'label' => 'Liste des utilisateurs', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Utilisateur']->addChild('list_groupe', array('route' =>'les_groupe', 'label' => 'Liste des groupes', 'attributes' => array('class' => 'icomoon-icon-people')));
    	$menu['Utilisateur']->setChildrenAttribute('class', 'sub');
        
        return $menu;
    }
    
    /**
     * @param MenuItem $menu
     * @return MenuItem
     */
    public function adminMenu($menu)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$menu->addChild('Action', array('route' => 'les_actions', 'label' => 'Actions', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
    	$menu->addChild('Structure', array('route' => 'les_structures', 'label' => 'Structure', 'attributes' => array('class' => 'icomoon-icon-grid')));
        $menu->addChild('Utilisateur', array('route' => 'les_utilisateurs', 'label' => 'Utilisateurs', 'attributes' => array('class' => 'entypo-icon-users')));
        $menu->addChild('Domaine', array('route' => 'les_domaine', 'label' => 'Domaine', 'attributes' => array('class' => 'icomoon-icon-earth')));
        $menu->addChild('TypeAction', array('route' => 'les_types_action', 'label' => 'Type Action', 'attributes' => array('class' => 'brocco-icon-type')));
        $menu->addChild('Instance_admin', array('route' => 'les_instance', 'label' => 'Instances', 'attributes' => array('class' => 'icomoon-icon-office')));
		$menu->addChild('Formule', array('route' => 'les_formules', 'label' => 'Formules', 'attributes' => array('class' => 'silk-icon-plus')));$menu->addChild('Reporting', array('route' => 'les_reportings', 'label' => 'Reporting', 'attributes' => array('class' => 'icomoon-icon-rotate-2')));
		if($user->getStructure()->getBuPrincipal()->getSignalisation() == 1){
			$menu->addChild('Signalisation', array('route' => 'les_signalisations', 'Label' => 'Signalisation', 'attributes' => array('class' => 'entypo-icon-warning')));
			$menu['Signalisation']->addChild('statSignal', array('uri' =>'#', 'label' => 'Signalisation', 'attributes' => array('class' => 'icomoon-icon-bars-2')));
			$menu['Signalisation']->addChild('Signalisation', array('route' => 'les_signalisations', 'Label' => 'Signalisation', 'attributes' => array('class' => 'entypo-icon-warning')));
			$menu['Signalisation']->setChildrenAttribute('class', 'sub');
		}
		
    	$menu->addChild('Reporting', array('route' => 'les_reportings', 'label' => 'Reporting', 'attributes' => array('class' => 'icomoon-icon-rotate-2')));
    	$menu['Action']->addChild('ajout_action', array('route' =>'nouvelle_action', 'label' => 'Ajouter une action', 'attributes' => array('class' => 'icomoon-icon-plus')));
    	$menu['Action']->addChild('liste_action', array('route' =>'les_actions', 'label' => 'Liste des actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('mes_action', array('route' =>'mes_actions', 'label' => 'Mes actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('importer_action', array('route' =>'chargement_action', 'label' => 'Importer des actions', 'attributes' => array('class' => 'icomoon-icon-download')));
    	//$menu['Action']->addChild('action_perso', array('uri' =>'#', 'label' => 'Mes actions perso', 'attributes' => array('class' => 'icomoon-icon-user')));
    	$menu['Action']->addChild('action_cyclique', array('route' =>'actioncyclique', 'label' => 'Action cyclique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	if($user->getStructure()->getBuPrincipal()->hasconfig(Config::BU_ACTION_GENERIQUE)==true)
    			$menu['Action']->addChild('action_generique', array('route' =>'les_actiongeneriques', 'label' => 'Action générique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	$menu['Action']->setChildrenAttribute('class', 'sub');

		$menu['Structure']->addChild('liste_structure', array('route' =>'les_structures', 'label' => 'Liste des structures', 'attributes' => array('class' => 'icomoon-icon-list')));
		$menu['Structure']->setChildrenAttribute('class', 'sub');

    	$menu['Instance_admin']->addChild('ajout_instance', array('route' =>'nouvelle_instance', 'label' => 'Ajouter une instance', 'attributes' => array('class' => 'icomoon-icon-plus')));
    	$menu['Instance_admin']->addChild('import_instance', array('uri' =>'#', 'label' => 'Importer des instances', 'attributes' => array('class' => 'icomoon-icon-download')));
    	$menu['Instance_admin']->addChild('list_instance', array('route' =>'les_instance', 'label' => 'Liste des instances', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Instance_admin']->setChildrenAttribute('class', 'sub');
     
    	$menu['Utilisateur']->addChild('ajout_utilisateur', array('route' =>'fos_user_registration_register', 'label' => 'Ajouter un utilisateur', 'attributes' => array('class' => 'icomoon-icon-user-plus-2')));
    	$menu['Utilisateur']->addChild('import_utilisateur', array('route' =>'chargement_utilisateur', 'label' => 'Importer des utilisateurs', 'attributes' => array('class' => 'icomoon-icon-download')));
    	$menu['Utilisateur']->addChild('list_utilisateur', array('route' =>'les_utilisateurs', 'label' => 'Liste des utilisateurs', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Utilisateur']->addChild('list_groupe', array('route' =>'les_groupe', 'label' => 'Liste des groupes', 'attributes' => array('class' => 'icomoon-icon-people')));
    	$menu['Utilisateur']->setChildrenAttribute('class', 'sub');
    
    	return $menu;
    }
    
    
    /**
     * @param MenuItem $menu
     * @return MenuItem
     */
    public function managerMenu($menu,$user)
    {
    	$menu->addChild('Action', array('route' =>'les_actions', 'label' => 'Actions', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
    	$menu->addChild('Collaborateurs', array('uri' =>$this->container->get('router')->generate('actions_collaborateurs', array('structure_id' =>$user->getStructure()->getId() )), 'label' => 'PA Collaborateurs', 'attributes' => array('class' => 'icomoon-icon-tree-3')));
    	
    	$menu['Action']->addChild('liste_action', array('route' =>'les_actions', 'label' => 'Liste des actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('mes_action', array('route' =>'mes_actions', 'label' => 'Mes actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('action_perso', array('uri' =>'#', 'label' => 'Mes actions perso', 'attributes' => array('class' => 'icomoon-icon-user')));
    	$menu['Action']->addChild('action_cyclique', array('route' =>'actioncyclique', 'label' => 'Action cyclique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	$menu['Action']->setChildrenAttribute('class', 'sub');
    	
    	return $menu;
    }
    /**
     * @param MenuItem $menu
     * @return MenuItem
     */
    public function simpleMenu($menu,$user)
    {
    	$menu->addChild('Action', array('route' =>'les_actions', 'label' => 'Actions', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
    	$menu->addChild('Statistique', array('uri' => '#', 'label' => 'Statistiques', 'attributes' => array('class' => 'icomoon-icon-stats')));
    	$menu['Statistique']->addChild('vuestat', array('route' =>'vue_statique', 'label' => 'Vue Statique', 'attributes' => array('class' => 'icomoon-icon-stats-up')));
    	$menu['Statistique']->addChild('vueevo', array('route' =>'vue_evolutive', 'label' => 'Vue Evolutive', 'attributes' => array('class' => 'icomoon-icon-stats-up')));
    			 
    	$menu['Statistique']->setChildrenAttribute('class', 'sub');
    	 
    	$menu['Action']->addChild('liste_action', array('route' =>'les_actions', 'label' => 'Liste des actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('mes_action', array('route' =>'mes_actions', 'label' => 'Mes actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	//$menu['Action']->addChild('action_perso', array('uri' =>'#', 'label' => 'Mes actions perso', 'attributes' => array('class' => 'icomoon-icon-user')));
    	$menu['Action']->addChild('action_cyclique', array('route' =>'actioncyclique', 'label' => 'Action cyclique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	if($user->getStructure()->getBuPrincipal()->hasconfig(Config::BU_ACTION_GENERIQUE) && ($user->hasRole(Utilisateur::ROLE_ANIMATEUR_ACTIONGENERIQUE) || $user->hasRole(Utilisateur::ROLE_ADMIN) )  )
    		$menu['Action']->addChild('action_generique', array('route' =>'les_actiongeneriques', 'label' => 'Action générique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	$menu['Action']->setChildrenAttribute('class', 'sub');
    	
    	return $menu;
    }
	 public function animateurMenu($menu,$user){
	 	$user = $this->container->get('security.context')->getToken()->getUser();
	 	$anim=$user->getAnimators();
    	$instances=array();
    	foreach($anim as $an)
    		$instances[$an->getInstance()->getId()] = substr($an->getInstance()->getLibelle(), 0, 20);
    	
    	$menu->addChild('Action', array('route' =>'les_actions', 'label' => 'Actions', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
    	$menu->addChild('Instance_anim', array('route' =>'les_instance', 'label' => 'Mes instances', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
        foreach($instances as $key => $inst){
        	$menu['Instance_anim']->addChild($inst, array('uri' => $this->container->get('router')->generate('les_actions_by_instance', array('instance_id' => $key)), 'label' => $inst, 'attributes' => array('class' => 'icomoon-icon-list')));
        }
    	$menu['Instance_anim']->setChildrenAttribute('class', 'sub');
    	$menu['Action']->addChild('ajout_action', array('route' =>'nouvelle_action', 'label' => 'Ajouter une action', 'attributes' => array('class' => 'icomoon-icon-plus')));
    	$menu['Action']->addChild('liste_action', array('route' =>'les_actions', 'label' => 'Liste des actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('mes_action', array('route' =>'mes_actions', 'label' => 'Mes actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('importer_action', array('route' =>'chargement_action', 'label' => 'Importer des actions', 'attributes' => array('class' => 'icomoon-icon-download')));
    //	$menu['Action']->addChild('action_perso', array('uri' =>'#', 'label' => 'Mes actions perso', 'attributes' => array('class' => 'icomoon-icon-user')));
    	$menu['Action']->addChild('action_cyclique', array('route' =>'actioncyclique', 'label' => 'Action cyclique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	if($user->getStructure()->getBuPrincipal()->hasconfig(Config::BU_ACTION_GENERIQUE) && ($user->hasRole(Utilisateur::ROLE_ANIMATEUR_ACTIONGENERIQUE) || $user->hasRole(Utilisateur::ROLE_ADMIN) )  )
    		$menu['Action']->addChild('action_generique', array('route' =>'les_actiongeneriques', 'label' => 'Action générique', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->setChildrenAttribute('class', 'sub');
    	if($user->getStructure()->getBuPrincipal()->getSignalisation() == 1){
    		$menu->addChild('Signalisation', array('route' => 'les_signalisations', 'Label' => 'Signalisation', 'attributes' => array('class' => 'entypo-icon-warning')));
    		$menu['Signalisation']->addChild('listeSign', array('route' =>'les_signalisations', 'label' => 'Les signalisations', 'attributes' => array('class' => 'minia-icon-list-4')));
    		$menu['Signalisation']->setChildrenAttribute('class', 'sub');
    	}
    	$menu->addChild('Reporting', array('route' => 'les_reportings', 'label' => 'Reporting', 'attributes' => array('class' => 'icomoon-icon-rotate-2')));
    	return $menu;
    	
    }
    /**
     * 
     * @param MenuItem $menu
     * @return MenuItem
     */
    public function rapporteurMenu($menu,$user)
    {
    	$structures=$user->getRapporteurStructure();
		
    	$menu->addChild('Structure', array('route' => 'les_structures', 'label' => 'Structure', 'attributes' => array('class' => 'icomoon-icon-grid')));
		$menu['Structure']->addChild('liste_structure', array('route' =>'les_structures', 'label' => 'Liste des structures', 'attributes' => array('class' => 'icomoon-icon-list')));
    	foreach($structures as $struct)
    		$menu['Structure']->addChild($struct->getLibelle(), array('uri' =>$this->container->get('router')->generate('les_actions_by_structure', array('structure_id' =>$struct->getId() )), 'label' => $struct->getLibelle(), 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Structure']->setChildrenAttribute('class', 'sub');
		
    	$menu->addChild('Reporting', array('route' => 'les_reportings', 'label' => 'Reporting', 'attributes' => array('class' => 'icomoon-icon-rotate-2')));
    	 
    	return $menu;
    }
    
    /**
     * @param MenuItem $menu
     * @param Utilisateur $user
     * @return MenuItem
     */
    public function membreEspaceMenu($menu, $user) {
    	$espaces_gest=array();
    	$espaces_mmb=array();
    	$mmb=$user->getMembreEspace();
    	foreach ($mmb as $me) {
    		if($me->getIsGestionnaire()==true) {
    			$espaces_gest[]=$me->getEspace();
    		} else {
    			$espaces_mmb[]=$me->getEspace();
    		}
    	}
    	$menu->addChild('Espace', array('uri' => '#', 'label' => 'Espaces', 'attributes' => array('class' => 'icomoon-icon-stats')));
    	foreach($espaces_gest as $esp) {
    		$menu['Espace']->addChild($esp->getLibelle(), array('uri' =>$this->container->get('router')->generate('dashboard_espace',array('espace_id'=>$esp->getId())), 'label' => $esp->getLibelle(), 'attributes' => array('class' => 'icomoon-icon-list')));
    	}
   		foreach($espaces_mmb as $esp) {
   			$menu['Espace']->addChild($esp->getLibelle(), array('uri' =>$this->container->get('router')->generate('les_actions_by_espace',array('espace_id'=>$esp->getId())), 'label' => $esp->getLibelle(), 'attributes' => array('class' => 'icomoon-icon-list')));
   		}
		$menu['Espace']->setChildrenAttribute('class', 'sub');
		return $menu;
    }
    
    /**
     * @param MenuItem $menu
     * @return MenuItem
     */
    public function sourceMenu($menu)
    {
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$menu->addChild('Action', array('route' =>'les_actions', 'label' => 'Actions', 'attributes' => array('class' => 'icomoon-icon-wand-2')));
		
    	if($user->getStructure()->getBuPrincipal()->getSignalisation() == 1) {
    		$menu->addChild('Signalisation', array('route' =>'les_signalisations', 'label' => 'Signalisation', 'attributes' => array('class' => 'entypo-icon-warning')));
    	}
    	$menu['Action']->addChild('liste_action', array('route' =>'les_actions', 'label' => 'Liste des actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	$menu['Action']->addChild('mes_action', array('route' =>'mes_actions', 'label' => 'Mes actions', 'attributes' => array('class' => 'icomoon-icon-list')));
    	//$menu['Action']->addChild('action_perso', array('uri' =>'#', 'label' => 'Mes actions perso', 'attributes' => array('class' => 'icomoon-icon-user')));
    	$menu['Action']->addChild('action_cyclique', array('route' =>'actioncyclique', 'label' => 'Action cyclique', 'attributes' => array('class' => 'cut-icon-reload ')));
    	if($user->getStructure()->getBuPrincipal()->hasconfig(Config::BU_ACTION_GENERIQUE) && ($user->hasRole(Utilisateur::ROLE_ANIMATEUR_ACTIONGENERIQUE) || $user->hasRole(Utilisateur::ROLE_ADMIN) )  )
    		$menu['Action']->addChild('action_generique', array('route' =>'les_actiongeneriques', 'label' => 'Action générique', 'attributes' => array('class' => 'icomoon-icon-list ')));
    	$menu['Action']->setChildrenAttribute('class', 'sub');
    	return $menu;
    }
}

