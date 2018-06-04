<?php
namespace Orange\MainBundle\Query;

use Orange\MainBundle\Entity\Statut;
use Doctrine\ORM\ORMException;

class ActionQuery extends BaseQuery {

	public function createTable($next_id) {
		$statement = $this->connection->prepare(sprintf("DROP TABLE IF EXISTS `temp_action`;
			CREATE TABLE IF NOT EXISTS `temp_action` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `reference` varchar(100) DEFAULT NULL,
			  `prenoms` varchar(150) DEFAULT NULL,
			  `email` varchar(50) NOT NULL,
			  `instance` varchar(100) DEFAULT NULL,
			  `contributeur` longtext COLLATE utf8_unicode_ci,
			  `statut` varchar(40) DEFAULT NULL,
			  `code_statut` varchar(40) DEFAULT NULL,
			  `type_action` varchar(100) DEFAULT NULL,
			  `domaine` varchar(50) DEFAULT NULL,
			  `date_debut` varchar(10) DEFAULT NULL,
			  `date_initial` varchar(10) DEFAULT NULL,
			  `date_cloture` varchar(10) DEFAULT NULL,
			  `libelle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `description` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
			  `priorite` varchar(45) NULL,		  
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=%s;", $next_id));
		$statement->execute();
	}
	
	public function loadTable($fileName, $web_dir,$next_id,$isCorrective) {
		$newPath = $this->loadDataFile($fileName, 'action', $web_dir);
		/*$erreurAction = null;
		$handle = fopen($newPath, 'r');
		$numberColumns = count(fgetcsv($handle, null, ';'));
		$index = 1;
		while(($line = fgets($handle)) !== FALSE) {
			$index++;
			if(count(explode(';', $line))!=$numberColumns) {
				$erreurAction .= sprintf("Le nombre de colonnes à la ligne %s est incorrect<br>", $index);
			}
		}
		fclose($handle);
		if($erreurAction) {
			throw new ORMException($erreurAction);
		}*/
		/*Insertion du chargement du fichier téléchargé dans la table temporaire*/
		if($isCorrective==0) {
				$query="LOAD DATA LOCAL INFILE '$newPath' INTO TABLE temp_action
						CHARACTER SET latin1
						FIELDS TERMINATED BY  ';' ENCLOSED BY  '".'"'."'
						LINES TERMINATED BY  '\\r\\n'
						IGNORE 1 LINES
						(`prenoms`, `email`,`instance`, `contributeur`, `statut` ,`type_action`,`domaine`, @date_debut, @date_initial, @date_cloture,
						`libelle`, `description`,`priorite`) set date_debut = STR_TO_DATE(@date_debut, '%d/%m/%Y'), 
						date_initial = STR_TO_DATE(@date_initial, '%d/%m/%Y'), date_cloture = STR_TO_DATE(@date_cloture, '%d/%m/%Y')";
		} else {
				$query="LOAD DATA LOCAL INFILE '$newPath' INTO TABLE temp_action
						CHARACTER SET latin1
						FIELDS TERMINATED BY  ';' ENCLOSED BY  '".'"'."'
						LINES TERMINATED BY  '\\r\\n'
						IGNORE 1 LINES
						(`reference`,`prenoms`, `email`,`instance`, `contributeur`,
						`statut` ,`type_action`,`domaine`,
						@date_debut, @date_initial, @date_cloture,`libelle`, `description`,`priorite`) set date_debut = STR_TO_DATE(@date_debut, '%d/%m/%Y'), 
						date_initial = STR_TO_DATE(@date_initial, '%d/%m/%Y'), date_cloture = STR_TO_DATE(@date_cloture, '%d/%m/%Y');";
		}
		 $this->connection->prepare($query)->execute();
	}
	
	/**
	 * @throws \Exception
	 * @return number
	 */
	public function updateTable($users, $instances, $statuts, $lesMails) {
		$query='';
		$erreurAction = null;
		$query .= "UPDATE temp_action t, utilisateur u SET t.email = u.id WHERE LOWER(TRIM(u.email)) LIKE LOWER(TRIM(t.email));";
		$query .= "UPDATE temp_action SET contributeur = REPLACE(contributeur, ' ', '');";
		for($i=0; $i<10;$i++) {
			$query .= "UPDATE temp_action t, utilisateur u SET t.contributeur = REPLACE(LOWER(TRIM(t.contributeur)), LOWER(TRIM(u.email)), u.id) WHERE t.contributeur LIKE CONCAT('%', LOWER(TRIM(u.email)), '%');";
		}
		$query .= "UPDATE temp_action t, priorite p SET t.priorite = p.id WHERE p.libelle LIKE t.priorite;";
		$query .= "UPDATE temp_action t INNER JOIN utilisateur u ON u.email LIKE t.email SET t.email = u.id;";
		$query .= "UPDATE temp_action t LEFT JOIN priorite p ON p.libelle LIKE t.priorite SET t.priorite = NULL WHERE p.id IS NULL;";
		$query .= "UPDATE temp_action t INNER JOIN priorite p ON p.libelle LIKE t.priorite SET t.priorite = p.id;";
		for($i = 0; $i < count($this->special_char); $i ++) {
			if($this->special_char[$i]=="'") {
				$query .= 'UPDATE temp_action SET instance = REPLACE(instance, "'.$this->special_char[$i].'", "'.$this->replacement_char[$i].'");';
			} else {
				$query .= "UPDATE temp_action SET instance = REPLACE(instance, '".$this->special_char[$i]."', '{$this->replacement_char[$i]}');";
			}
		}
		//$query .= "UPDATE temp_action t, statut st SET t.statut = st.id WHERE t.statut LIKE st.etat;";
		$query .= "UPDATE temp_action t, statut st SET t.code_statut = st.code WHERE LOWER(TRIM(t.statut)) LIKE LOWER(TRIM(st.statut));";
		$query .= "UPDATE temp_action t INNER JOIN instance i ON i.code LIKE t.instance SET t.instance = i.id;";
		$query .= "UPDATE temp_action t INNER JOIN domaine d ON d.libelle_domaine LIKE LOWER(TRIM(t.domaine)) INNER JOIN instance_has_domaine ihd ON ihd.domaine_id = d.id AND ihd.instance_id = t.instance SET t.domaine = d.id;";
		$query .= "UPDATE temp_action t INNER JOIN type_action ta ON ta.type LIKE LOWER(TRIM(t.type_action))  INNER JOIN instance_has_type_action iht ON iht.type_action_id = ta.id AND iht.instance_id = t.instance SET t.type_action = ta.id;";
		$this->connection->prepare($query)->execute();
		/*Avant de faire l'insertion, on vérifie si tous les updates ont été faits*/
		$resultsAction = $this->connection->fetchAll("SELECT id, contributeur , statut, code_statut, priorite, email ,type_action,  libelle, description, date_debut, date_initial, date_cloture, domaine, instance  from temp_action t");
		$erreurAction=null;
		for($i=0; $i<count($resultsAction);$i++) {
			$contributeurs = explode(',', $resultsAction[$i]['contributeur']);
			$contributeurId = null;
			foreach($contributeurs as $contributeur) {
				if(intval($contributeur)==0) {
					$contributeurId = $contributeur;
					break;
				}
			}if(!$resultsAction[$i]['date_initial']){
				$erreurAction .= sprintf("Le délai initial à la ligne %s n'est pas valide ou n'est pas renseigné<br>", $i+2);
			}if(!$resultsAction[$i]['date_debut']){
				$erreurAction .= sprintf("La date de début à la ligne %s n'est pas valide ou n'est pas renseignée<br>", $i+2);
			}
			if($contributeurId) {
				$erreurAction .= sprintf("L'e-mail du contributeur à la ligne %s n'existe pas<br>", $i+2);
			}
			if(ctype_digit($resultsAction[$i]['email'])==false) {
				$erreurAction .= sprintf("Le porteur à la ligne %s n'existe pas<br>", $i+2);
			}
// 			if(!empty($membres)){
// 				if(!in_array($resultsAction[$i]['email'], $membres)) {
// 					$erreurAction .= sprintf("Le porteur à la ligne %s n'est pas membre dans l'espace.<br>", $i+2);
// 				}
// 			}
			if(ctype_digit($resultsAction[$i]['domaine'])==false) {
				$erreurAction .= sprintf("Le domaine à la ligne %s n'existe pas<br>", $i+2);
			}
			if(ctype_digit($resultsAction[$i]['instance'])==false) {
				$erreurAction .= sprintf("L'instance à la ligne %s n'existe pas<br>", $i+2);
			}
			if(ctype_digit($resultsAction[$i]['type_action'])==false) {
				$erreurAction .= sprintf("Le type d'action à la ligne %s n'existe pas<br>", $i+2);
			}
			if(is_null($resultsAction[$i]['code_statut'])) {
				$erreurAction .= sprintf("Le statut à la ligne %s n'existe pas<br>", $i+2);
			} elseif(in_array($resultsAction[$i]['code_statut'], array(Statut::ACTION_SOLDEE, Statut::ACTION_ABANDONNEE)) && !$resultsAction[$i]['date_cloture']) {
				$erreurAction .= sprintf("La date de cloture à la ligne %s n'est pas valide ou n'est pas renseignée<br>", $i+2);
			}
			if($resultsAction[$i]['priorite'] && ctype_digit($resultsAction[$i]['priorite'])==false) {
				$erreurAction .= sprintf("La priorité à la ligne %s n'existe pas<br>", $i+2);
			}
		}
		if($erreurAction) {
			throw new ORMException($erreurAction);
		}
		$id = array();
		foreach ($resultsAction as $value){
			array_push($id, $value['id']);
		}
		return array('nbr' => count($resultsAction), 'id' => $id) ;
		
	}
	/**
	 * @param unknown $nouvelle_statut
	 * @param \Orange\MainBundle\Entity\Utilisateur $current_user
	 */
	public function migrateData($nouvelle_statut, $current_user, $isCorrective) {
		$ref = $isCorrective==0 ? "CONCAT('A_', t.id)" : "CONCAT(t.reference, CONCAT('-A_', t.id))";
		$query = "update temp_action t set t.code_statut = CASE
				WHEN t.code_statut = '" . Statut::ACTION_SOLDEE . "' and ((t.date_cloture!='' and t.date_initial < t.date_cloture ) or (t.date_cloture='' and t.date_initial < NOW())) THEN '" . Statut::ACTION_SOLDEE_HORS_DELAI . "'
				WHEN t.code_statut = '" . Statut::ACTION_SOLDEE . "' and ((t.date_cloture!='' and t.date_initial >= t.date_cloture ) or (t.date_cloture!='' and t.date_initial >= NOW() )) THEN '" . Statut::ACTION_SOLDEE_DELAI . "'
				WHEN t.code_statut = '" . Statut::ACTION_EN_COURS . "' and t.date_initial<NOW() THEN '" . Statut::ACTION_ECHUE_NON_SOLDEE . "'
				WHEN t.code_statut = '" . Statut::ACTION_EN_COURS . "' and t.date_initial>=NOW() THEN '" . Statut::ACTION_NON_ECHUE . "'
				END
				where t.code_statut = '" . Statut::ACTION_SOLDEE . "' or t.code_statut = '" . Statut::ACTION_EN_COURS . "';";
		
		$query .= "INSERT INTO action (`id`, `reference`, `priorite_id`, `type_action_id`, 
						   			`libelle`, `description`, `date_action`, `date_debut`, `date_fin_prevue`,
									`date_initial`, `date_cloture`, `date_fin_execution`, `domaine_id`,`instance_id`,`etat_courant`, `etat_reel`, `porteur_id`, 
									`animateur_id`) 
					select t.id,".$ref.", t.priorite, t.type_action, 
								  	 t.libelle, t.description, CURRENT_TIMESTAMP(), date_debut, date_initial, date_initial, date_cloture, date_cloture, 
				                     t.domaine,  t.instance, t.code_statut, t.code_statut, t.email," . $current_user->getId () . "
								  	 from temp_action t;";
		
		$query .= "INSERT INTO action_has_statut (`id` ,`action_id`,`statut_id`,`dateStatut`,`utilisateur_id`,`commentaire`) 
					select null, t.id, st.id, NOW()," . $current_user->getId () . ", 'action importée avec succés'
					from temp_action t
					inner join statut st on st.code =t.code_statut;";
		if ($isCorrective == 1){
			$query .= "INSERT INTO action_has_signalisation (`action_id`, `signalisation_id`)
			           select t.id, s.id from temp_action t INNER JOIN signalisation s ON s.reference = t.reference;";
				
			$query .= "UPDATE signalisation s 
					  LEFT JOIN temp_action t on s.reference = t.reference 
					  SET s.etat_courant = 'SIGN_PRISE_EN_CHARGE' 
					  WHERE t.id is not null;";
		}
		$query2 = "";
		$resultsAction = $this->connection->fetchAll("SELECT id , contributeur from temp_action ");
		for($i=0; $i<count($resultsAction);$i++) {
			$id=$resultsAction[$i]['id'];
			$idsContrib=\explode(',', $resultsAction[$i]['contributeur']);
			foreach ($idsContrib as $val){
				if(strlen($val)>0){
					$query2.="INSERT INTO contributeur (`id` ,`action_id`,`utilisateur_id`) values";
					$query2 .= "(null,".$id.",".$val.");";
				}
			}
		}
		$this->connection->prepare($query)->execute();
		if(strlen($query2)>0)
	    	$this->connection->prepare($query2)->execute();
		$query4 = "UPDATE action set priorite_id=null where priorite_id=0;";
		$this->connection->prepare($query4)->execute();
	}
	
	public function deleteTable() {
 		$statement = $this->connection->prepare(sprintf("DROP TABLE IF EXISTS `temp_action`;"));
 		$statement->execute();
	}
	
	public function miseAJourEntity(){
		$sql = "UPDATE action a, utilisateur u SET a.structure_id = u.structure_id 
				WHERE a.porteur_id = u.id 
					AND  a.structure_id is not null 
					AND a.etat_courant NOT like '%ACTION_SOLDEE%'
					AND a.etat_courant NOT like '%ABANDONNEE%'";
		$this->connection->prepare($sql)->execute();
	}
	
	public function updateId($table){
		$sql = "SET  @num := 0;";
		$sql .= "UPDATE $table SET id = @num := (@num+1);";
		$sql .= "ALTER TABLE $table AUTO_INCREMENT =1;";
		$this->connection->prepare($sql)->execute();
	}
}