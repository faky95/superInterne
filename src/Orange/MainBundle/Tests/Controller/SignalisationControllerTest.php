<?php

namespace Orange\MainBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SignalisationControllerTest extends WebTestCase
{
	/**
	 * @var \Orange\QuickMakingBundle\Model\EntityManager
	 */
	private $_em;
	
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    /*
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/les_signalisations/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /les_signalisations/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'orange_mainbundle_signalisation[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'orange_mainbundle_signalisation[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

    */   
    
    /**
     * Création d'une signalisation
     */
    public function testCreate()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/nouvelle_signalisation/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Résultat inattendu pour la requête <nouvelle_signalisation>");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'orange_mainbundle_signalisation[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'orange_mainbundle_signalisation[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    	$dispatcher = $this->container->get('event_dispatcher');
    	$em = $this->getDoctrine()->getManager();
        $entity = new Signalisation();
        $form = $this->createCreateForm($entity,'Signalisation', array(
        														'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())));
        $form->handleRequest($request);
        if ($request->getMethod() == 'POST' ) {
	        if ($form->isValid()) {
	            $em = $this->getDoctrine()->getManager();
	            $em->persist($entity);
	            $instance_id = $entity->getInstance()->getId();
	            $source = $em->getRepository('OrangeMainBundle:Source')->findOneBy(array('instance'=>$instance_id, 'utilisateur'=>$this->getUser()->getId()));
	            $entity->setSource($source);
	            $em->flush();
	            SignalisationUtils::setReferenceSignalisation($em, $entity);
	            SignalisationUtils::changeStatutSignalisation($em, $this->getUser(), Statut::NOUVELLE_SIGNALISATION, $entity, "Nouvelle signalisation ajoutée. En attente de prise en charge !");
	            $event = $this->get('orange_main.signalisation_event')->createForSignalisation($entity);
	            $dispatcher->dispatch(OrangeMainEvents::SIGNALISATION_CREATE_NOUVELLE, $event);
	            $this->get('session')->getFlashBag()->add('success', array (
									            		  'title' => 'Notification', 'body' => 'Enrégistrement effectué avec succès'
	            ));
	            return $this->redirect($this->generateUrl('details_signalisation', array('id' => $entity->getId())));
	        }
	        if(!$form->isValid()) {
	        	$this->get('session')->getFlashBag()->add('error', array (
									        			  'title' => 'Notification', 'body' => 'Une erreur est survenue. Veuillez réessayer.'
	        	));
	        }
        }
        return array('entity' => $entity, 'form'   => $form->createView());
    }
    
   /**
     * Modifier une signalisation
     */
    public function testUpdateSignalisation()
    {
    	$em = $this->getDoctrine()->getManager();
    	$entity = $em->getRepository('OrangeMainBundle:Signalisation')->find($id);
    	$form = $this->createCreateForm($entity, 'Signalisation', array(
        					'attr' => array('user_id' => $this->getUser()->getId(), 'structure_id' => $this->getUser()->getStructure()->getId())
    		));
    	$request = $this->get('request');
    	$today = new \DateTime();
    	$today = $today->format('Y-m-d');
    	if ($request->getMethod() == 'POST') {
    		$form->handleRequest($request);
    		if ($form->isValid()) {
    			$em->persist($entity);
    			$em->flush();
    			return $this->redirect($this->generateUrl('edition_signalisation', array('id' => $id)));
    		}
    	}
    	return array('entity' => $entity, 'edit_form' => $form->createView());
    }

    /**
     * Liste des périmètres d'une instance
     */
    public function testListInstance() {
    	$instance = $this->_em->getRepository('OrangeMainBundle:Instance')->find(139);
    	$arrData = $this->_em->createQueryBuilder('i')
    		->where('i.parent = :p')
    		->setParameter('p', 139)
    		->getQuery()->getArrayResult();
    	if($instance) {
    		$this->assertEquals($instance->getChildren()->count(), count($arrData));
    	}
    }
    
    /**
     * Importer des signalisations
     */
    public function testImport() {
        $form = $this->createForm(new LoadingType());
        $em   = $this->getDoctrine()->getManager();
        $sources = $em->getRepository('OrangeMainBundle:Source')->getAllSources();
        $form->handleRequest($request);
        if($form->isValid()) {
            $data = $form->getData();
            try {
                $number = $this->get('orange.main.loader')->loadSignalisation($data['file'],$sources, $this->getUser());
           		$signs = $em->getRepository('OrangeMainBundle:Signalisation')->getSignalisations($number['id']);
                $nbr = $number['nbr'];
                foreach ($signs as $value){
                	$helper = $this->get('orange.main.mailer');
                	$subject = "Nouvelle Signalisation";
                	$instance = $value->getInstance();
                	$animateur = $instance->getAnimateur()->count()==0
						? $instance->getParent()->getAnimateur()->get(0)->getUtilisateur()->getNomComplet()
						: $instance->getAnimateur()->get(0)->getUtilisateur()->getNomComplet();
                	$destinataire = InstanceUtils::animateursEmail($em, $instance);
                	$commentaire = 'Une nouvelle signalisation a été postée par '.$this->getUser()->getCompletNom().' au périmétre: '.$instance->getLibelle().'. '
                			.$animateur.' est prié de prendre en charge cette signalisation. ';
                	$signStatut = $em->getRepository('OrangeMainBundle:SignalisationStatut')->getStatut($value->getId());
                	Notification::notificationSignWithCopy($helper, $subject, $destinataire, array($value->getSource()->getUtilisateur()->getEmail()), $commentaire, $signStatut[0]);
                	//$this->get('orange.main.mailer')->notifNewSignalisation($destinataire, array($value->getSource()->getUtilisateur()->getEmail()), $value);
                }
                $this->get('session')->getFlashBag()->add('success', "Le chargement s'est effectué avec succés! Nombre de signalisation chargé: $nbr");
                return $this->redirect($this->generateUrl('les_signalisations'));
            } catch(ORMException $e) {
            	$this->get('session')->getFlashBag()->add('error', array ('title' => "Message d'erreur", 'body' => nl2br($e->getMessage())
            	));
            }
        }
        return $this->render('OrangeMainBundle:Signalisation:loading.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * Liste des types d'action d'une instance
     */
    public function testListTypeSignalisationByInstance() {
        // Create a new client to browse the application
        $client = static::createClient();
        // Create a new entry in the database
        $crawler = $client->request('POST', '/domaine_signalisation_by_instance/', array('id' => 201));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Résultat inattendu pour la requête <domaine_signalisation_by_instance>");
        $arrData = json_decode($client->getResponse()->getContent(), true);
    	$instance = $this->_em->getRepository('OrangeMainBundle:Instance')->find(201);
    	if($instance) {
    		$id = $instance->getParent() ? $instance->getParent()->getId() : $instance->getId();
    		$number = $this->_em->getRepository('OrangeMainBundle:TypeAction')->listTypeByInstance($id);
    		$this->assertEquals(count($arrData) - 1, $number, 'Le nombre de type de signalisation retourné est incorrect');
    	}
    }
    
    /**
     * Liste des domaines d'une instance parente et d'un périmétre
     */
    public function testListDomaineSignalisationByInstance() {
        // Create a new client to browse the application
        $client = static::createClient();
        // Create a new entry in the database
        $crawler = $client->request('POST', '/domaine_signalisation_by_instance/', array('id' => 201));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Résultat inattendu pour la requête <domaine_signalisation_by_instance>");
        $arrData = json_decode($client->getResponse()->getContent(), true);
    	$instance = $this->_em->getRepository('OrangeMainBundle:Instance')->find(201);
    	if($instance) {
    		$number = (!$instance->getParent() && $instance->getConfiguration())
    			? count($this->_em->getRepository('OrangeMainBundle:Domaine')->listByInstance($instance->getId()))
    			: count($this->_em->getRepository('OrangeMainBundle:Domaine')->listDomaineByInstance($instance->getParent(), $instance->getLibelle()));
    		$this->assertEquals(count($arrData) - 1, $number, 'Le nombre de domaine retourné est incorrect');
    	}
    }
    
}
