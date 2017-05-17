<?php
namespace Orange\MainBundle\Service;

use Orange\MainBundle\Entity\Utilisateur;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\DBAL\DBALException;
use Orange\MainBundle\Entity\ActionGenerique;

class Query {
		
	
	
	protected $container;
	
	
	
	public function __construct(ContainerInterface $container) 
	{
		$this->container = $container;
	}
	
	
	
	/**
	 * 	L'utilisateur est porteur d'une action,
	 *  liste total des différents statuts de la dernière semaine
	 *  groupé par instance.
	 *  typeActeur = Porteur
	 */
	public function statistiquePorteur($user_id)
	{
		$connexion = $this->container->get('database_connection');
		$entityManager = $this->container->get('doctrine')->getEntityManager();
		$sql = 'SELECT s.code as type_statut, inst.id as instance_id, COUNT(action_id) AS nombre 
				FROM action_has_statut AS stat 
				LEFT JOIN statut AS s ON stat.statut_id = s.id 
				LEFT JOIN action AS ac ON stat.action_id = ac.id
				LEFT JOIN utilisateur AS p ON ac.porteur_id = p.id 
				LEFT JOIN instance AS inst ON ac.instance_id = inst.id
				WHERE ac.porteur_id = ?
				AND stat.dateStatut >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY
				AND stat.dateStatut < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY
				GROUP BY s.code, inst.id'
				;
				$statement = $entityManager->getConnection()->prepare($sql);
				$statement->bindValue(1, $user_id);
				$statement->execute();
				return $statement->fetchAll();
	}
	
	
	/**
	 * 	L'utilisateur appartient à des groupes
	 *  liste total des différents statuts de la dernière semaine
	 *  
	 *  groupé par instance.
	 */
	public function statistiqueInGroup($action_id, $listeActionId)
	{
		$connexion = $this->container->get('database_connection');
		$entityManager = $this->container->get('doctrine')->getEntityManager();
		$sql = 'SELECT s.code as type_statut, inst.id as instance_id, COUNT(action_id) AS nombre
				FROM action_has_statut AS stat
				LEFT JOIN statut AS s ON stat.statut_id = s.id
				LEFT JOIN action AS ac ON stat.action_id = ac.id
				LEFT JOIN instance AS inst ON ac.instance_id = inst.id
				WHERE ac.id = ?
				AND stat.dateStatut >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY
				AND stat.dateStatut < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY
				GROUP BY s.code, inst.id'
				;
				$statement = $entityManager->getConnection()->prepare($sql);
				$statement->bindValue(1, $action_id);
				$statement->execute();
				return $statement->fetchAll();
	}
	
	public function orienterManyActions($datas){
		$connection  =  $this->container->get('database_connection');
		$ids         =  is_array($datas['ids'])==true ? $datas['ids'] : array($datas['ids']);
		$stringfyIds =  implode(',', $ids);
		$user        =  $datas['user'];
		/** @var ActionGenerique $actionGenerique */
		$actionGenerique = $datas['actiongenerique'];
		$sql     = "SELECT reference from action where date_initial > '".$actionGenerique->getDateInitial()->format("Y-m-d")."'  and id in (".$stringfyIds.")";
		$results = $connection->fetchAll($sql);
		if(count($results)>0) {
			if(count($ids)>1){
				$refs    = implode(' ; ', array_column($results, 'reference'));
				$message = "Les délais initials des actions ({$refs}) doivent être <=  celui de l'action générique. ";
			}else {
				$message = "Le délai initial de l'action doit être inférieur ou égal à celui de l'action générique. ";
			}
			throw new DBALException($message);
		}
		$query = "";
		foreach ($ids as $id)
			$query .= "INSERT INTO `action_generique_has_action`(`action_id`, `utilisateur_id`, `date`, `commentaire`, `actionGenerique_id`) VALUES
					   (".$id.",".$user->getId().", NOW(), 'Orientation avec succés!', ".$actionGenerique->getId()." );";
		
		$connection->prepare($query)->execute();
	}
	
	
}
