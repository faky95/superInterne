<?php
namespace Orange\MainBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Orange\MainBundle\Entity\Utilisateur;
use Orange\MainBundle\Query\UtilisateurQuery;
use Orange\MainBundle\Entity\Signalisation;
use Orange\MainBundle\Query\SignalisationQuery;
use Orange\MainBundle\Query\BaseQuery;
use Orange\MainBundle\Query\ActionQuery;
use Orange\MainBundle\Entity\Statut;
use Symfony\Component\DependencyInjection\Container;

class Loader {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var \FOS\UserBundle\Mailer\TwigSwiftMailer
	 */
	protected $mailer;
	
	/**
	 * @var string
	 */
	protected $web_dir;
	
	/**
	 * @var array
	 */
	protected $_ids;
	
	/**
	 * @var array
	 */
	protected $_states;

	/**
	 * @param \Symfony\Component\DependencyInjection\Container $container
	 */
	public function __construct($container, $web_dir, $ids, $states) {
		$this->em = $container->get('doctrine.orm.entity_manager');
		//$this->mailer = $container->get('mailer');
		$this->web_dir = $web_dir;
		$this->_ids = $ids;
		$this->_states = $states;
	}
	  
	
	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param integer $buP
	 * @return integer
	 */
	public function loadUtilisateur($file, $buP) {
		$repository = $this->em->getRepository('OrangeMainBundle:Utilisateur'); 
		$query = new UtilisateurQuery($this->em->getConnection());
		$next_id = $repository->getNextId();
		$query->createTable($next_id);
		$query->loadTable($file->getPathname(), $this->web_dir); 
		$number = $query->updateTable();
		$query->migrateData($buP);
		//$query->sendMail($this->mailer);
		$query->deleteTable();
		return $number;
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param integer $buP
	 * @return integer
	 */
	public function loadStructure($file, $buP) {
		$query = new BaseQuery($this->em->getConnection());
		$this->em->getConnection()->beginTransaction();
		$repository = $this->em->getRepository('OrangeMainBundle:Structure');
		try {
			$content = file_get_contents($file->getPathname());
			$arrData = str_getcsv($content, "\n");
			if(isset($arrData[0])) {
				unset($arrData[0]);
			}
			$erreur = null;$line = 2;
			foreach($arrData as $data) {
				try {
					$repository->saveLine(explode(';', $data), $query, $buP,$line);
				} catch (\Exception $e) {
					$erreur .= ($erreur ? '<br>' : null).$e->getMessage();
				}
				$line++;
			}
			if($erreur) {
				throw new \Exception($erreur);
			}
			$this->em->getConnection()->commit();
			return count($arrData);
		} catch (\Exception $e) {
			$this->em->getConnection()->rollback();
			$this->em->close();
			throw $e;
		}
	} 
	
	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @return integer
	 */
	public function loadSignalisation($file,$sources,$current_user) {
		$repository = $this->em->getRepository('OrangeMainBundle:Signalisation');
		$nouvelle_statut=$this->em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code'=>Statut::NOUVELLE_SIGNALISATION));
		$query = new SignalisationQuery($this->em->getConnection());
		$next_id = $repository->getNextId();
		$query->crateTable($next_id);
		$query->loadTable($file->getPathname(), $this->web_dir);
		$number = $query->updateTable($sources);
		$query->migrateData($current_user,$nouvelle_statut);
		$query->deleteTable();
		return $number;
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @param \Orange\MainBundle\Entity\Utilisateur $current_user
	 * @return integer
	 */
	public function loadAction($file, $current_user, $users, $instances) {
		$repository = $this->em->getRepository('OrangeMainBundle:Action');
		$nouvelle_statut=$this->em->getRepository('OrangeMainBundle:Statut')->findOneBy(array('code'=>Statut::ACTION_NOUVELLE));
		$statuts=$this->em->getRepository('OrangeMainBundle:Statut')->getArrayStatutImport();
		$lesMails = array();
		$query = new ActionQuery($this->em->getConnection());
		$next_id = $repository->getNextId();
		//$query->createTable($next_id);
		$nl = $query->loadTable($file->getPathname(), $this->web_dir, $next_id);
		$number = $query->updateTable($users, $instances,$statuts,$lesMails);
		$query->migrateData($nouvelle_statut, $current_user, $nl);
		$query->deleteTable();
		return $number;
	}
	
}