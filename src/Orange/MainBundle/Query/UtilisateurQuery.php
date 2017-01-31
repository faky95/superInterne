<?php
namespace Orange\MainBundle\Query;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class UtilisateurQuery extends BaseQuery {

	public function createTable($next_id) {
		$statement = $this->connection->prepare(sprintf("DROP TABLE IF EXISTS `temp_utilisateur`;
			CREATE TABLE IF NOT EXISTS `temp_utilisateur` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `matricule` varchar(100) DEFAULT NULL,
			  `prenom` varchar(100) DEFAULT NULL,
 			  `nom` varchar(100) DEFAULT NULL,
			  `email` varchar(255) NOT NULL,
			  `username` varchar(255) NOT NULL,
			  `telephone` varchar(25) DEFAULT NULL,
			  `structure` varchar(255) DEFAULT NULL,
			  `manager` tinyint(1) DEFAULT NULL,
			  `bu` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=%s;", $next_id));
		$statement->execute();
	}
	
	public function loadTable($fileName, $web_dir) {
		$newPath = $this->loadDataFile($fileName, 'utilisateur', $web_dir);
		/*Insertion du chargement du fichier téléchargé dans la table temporaire*/
		$statement = $this->connection->prepare(sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE temp_utilisateur
			CHARACTER SET latin1
			FIELDS TERMINATED BY  ';'
			LINES TERMINATED BY  '\\r\\n'
			IGNORE 1 LINES
			(`matricule`, `prenom`, `nom`, `email`, `username`, `telephone`, `structure`, `manager`, `bu`);", $newPath));
		$statement->execute();
	}
	
	/**
	 * @throws \Exception
	 * @return number
	 */
	public function updateTable() {
		/*Mise à jour des activités par leur id respectif */
		$query = "UPDATE temp_utilisateur t, bu b SET t.bu = b.id WHERE b.libelle = t.bu;";
		$query .= "UPDATE temp_utilisateur t, structure s SET t.structure = s.id WHERE s.libelle = t.structure AND t.bu=s.bu_principal_id;";
		$query .= "UPDATE temp_utilisateur t INNER JOIN structure s ON s.id = t.structure;";
	    $this->connection->prepare($query)->execute();
	    
		$erreurUtilisateur = null;
		
		/*Avant de faire l'insertion, on vérifie si tous les updates ont été faits*/
		$resultsUtilisateur = $this->connection->fetchAll("SELECT id, matricule, prenom, nom, email, username, telephone, structure, manager,bu  from temp_utilisateur t");
		
		/*gestion des doublons */
		$trouveMatricule=$this->connection->fetchAll("SELECT matricule, count(id) as doublon   from temp_utilisateur t WHERE t.matricule IN (select u.matricule from utilisateur u)");
		$trouveEmail=$this->connection->fetchAll("SELECT email, count(id) as doublon   from temp_utilisateur t WHERE t.email IN (select u.email from utilisateur u)");
		$trouveLogin=$this->connection->fetchAll("SELECT username, count(id) as doublon   from temp_utilisateur t WHERE t.username IN (select u.username from utilisateur u)");
		if($trouveMatricule[0]['doublon']>0){
			$erreurUtilisateur .= ($erreurUtilisateur ? '<br>' : null).sprintf("Le matricule %s  existe deja ",$trouveMatricule[0]['matricule']);
		}
		if($trouveEmail[0]['doublon']>0){
			$erreurUtilisateur .= ($erreurUtilisateur ? '<br>' : null).sprintf("L'email  %s  existe deja ",$trouveEmail[0]['email']);
		}
		if($trouveLogin[0]['doublon']>0){
			$erreurUtilisateur .= ($erreurUtilisateur ? '<br>' : null).sprintf("Le login  %s  existe deja ",$trouveLogin[0]['username']);
		}
		for($i=0; $i<count($resultsUtilisateur);$i++) {
			if(intval($resultsUtilisateur[$i]['structure'])==0) {
		 		$erreurUtilisateur .= ($erreurUtilisateur ? '<br>' : null).sprintf("La structure à la ligne %s n'existe pas", $i);
			}
			if(intval($resultsUtilisateur[$i]['bu'])==0) {
				$erreurUtilisateur .= ($erreurUtilisateur ? '<br>' : null).sprintf("La direction à la ligne %s n'existe pas", $i);
			}
		}
		
		if($erreurUtilisateur) {
			throw new \Exception($erreurUtilisateur);
		}
		$id = array();
		foreach ($resultsUtilisateur as $value){
			array_push($id, $value['id']);
		}
		return array('nbr' => count($resultsUtilisateur), 'ids' => $id) ;
	}
	
	public function migrateData($buP) {
		$query= "INSERT INTO utilisateur (`id`, `matricule`, `prenom`, `nom`, 
											  		`email`, `email_canonical`, `username`, `username_canonical`,
											  		`telephone`, `structure_id`, `manager`,`enabled`, 
				  									`salt`,`password`,`roles`,`first_change_password`)
								  select t.id, t.matricule, t.prenom, t.nom, 
								  		t.email, t.email, t.username, t.username, 
								  		t.telephone, t.structure, t.manager, 1, 
								  		'5469zpurlvk04wkcggscwskcwo8ksc4', '6aDMsJOTSwb+393p77sYK3jBZ6Ej17XYNt0JV4abXpKwuAesLiGDl3s4lPzNttHD0Ztg05sOcK8J/d0SJnIN+Q==','a:0:{}',1
								  		from temp_utilisateur t"
			;
			$this->connection->prepare($query)->execute();
	}
	
	public function deleteTable() {
		$statement = $this->connection->prepare(sprintf("DROP TABLE IF EXISTS `temp_utilisateur`;"));
		$statement->execute();
	}
	
}