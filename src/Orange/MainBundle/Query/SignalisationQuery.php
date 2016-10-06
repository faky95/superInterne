<?php
namespace Orange\MainBundle\Query;

use Doctrine\ORM\ORMException;
class SignalisationQuery extends BaseQuery {


	public function crateTable($next_id) {
		$statement = $this->connection->prepare(sprintf("DROP TABLE IF EXISTS `tmp_signalisation`;
			CREATE TABLE IF NOT EXISTS `tmp_signalisation` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `libelle` varchar(45)  DEFAULT NULL,
			  `description` longtext COLLATE utf8_unicode_ci,
			  `perimetre` varchar(100) DEFAULT NULL,
			  `domaine` varchar(100) DEFAULT NULL,
			  `type` varchar(100) DEFAULT NULL,
			  `date_constat` varchar(10) DEFAULT NULL,
			  `constateur` varchar(50) DEFAULT NULL,
			  `source` varchar(50) DEFAULT NULL,
			  `site` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=%s;", $next_id));
		$statement->execute();
	}
	
	public function loadTable($fileName, $web_dir) {
		$newPath = $this->loadDataFile($fileName, 'signalisation', $web_dir);
		/*Insertion du chargement du fichier tÃ©lÃ©chargÃ© dans la table temporaire*/
		$statement = $this->connection->prepare(sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE tmp_signalisation
			CHARACTER SET latin1
			FIELDS TERMINATED BY  ';'
			LINES TERMINATED BY  '\\r\\n'
			IGNORE 1 LINES
			(`libelle`,`description`,`perimetre`,`domaine`,`type`, `date_constat`, `constateur`, `source`, `site`);", $newPath));
		$statement->execute();
	}
	
	/**
	 * @throws \Exception
	 * @return number
	 */
	public function updateTable($sources) {
		$query='';
// 		$query .= "UPDATE tmp_signalisation t INNER JOIN utilisateur u ON u.email LIKE t.source SET t.source = u.id;";
		$query .= "UPDATE tmp_signalisation t INNER JOIN utilisateur u ON u.email LIKE t.constateur SET t.constateur = u.id;";
		$query .= "UPDATE tmp_signalisation t INNER JOIN instance i ON i.libelle LIKE t.perimetre SET t.perimetre = i.id;";
		$query .= "UPDATE tmp_signalisation t INNER JOIN domaine d ON LOWER(d.libelle_domaine) LIKE LOWER(t.domaine) SET t.domaine = d.id;";
		$query .= "UPDATE tmp_signalisation t INNER JOIN type_action ta ON LOWER(ta.type) LIKE LOWER(t.type)SET t.type = ta.id;";
		foreach ($sources as $id => $val){
			$query .= sprintf("UPDATE tmp_signalisation t INNER JOIN utilisateur u ON u.email LIKE t.source SET t.source =".$val['id']." 
					        WHERE u.id =%s;",$val['user']);
		}
		$this->connection->prepare($query)->execute();
		/*Avant de faire l'insertion, on vÃ©rifie si tous les updates ont Ã©tÃ© faits*/
		$resultsAction = $this->connection->fetchAll("SELECT id, libelle, description, perimetre, domaine, type, date_constat, constateur, source , site from tmp_signalisation t");
		$erreurAction = null;
		for($i=0; $i<count($resultsAction);$i++) {
			if(intval($resultsAction[$i]['source'])==0) {
				$erreurAction .= sprintf("La source à la ligne %s n'existe pas<br>", $i+2);
			}
			if(intval($resultsAction[$i]['constateur'])==0) {
				$erreurAction .= sprintf("Le constateur à la ligne %s n'existe pas<br>", $i+2);
			}
			if(ctype_digit($resultsAction[$i]['domaine'])==false) {
				$erreurAction .= sprintf("Le domaine à la ligne %s n'existe pas<br>", $i+2);
			}
			if(ctype_digit($resultsAction[$i]['perimetre'])==false) {
				$erreurAction .= sprintf("Le périmetre à la ligne %s n'existe pas<br>", $i+2);
			}
			if(ctype_digit($resultsAction[$i]['type'])==false) {
				$erreurAction .= sprintf("Le type à la ligne %s n'existe pas<br>", $i+2);
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
	
	public function migrateData($current_user,$nouvelle_statut) {
		$query="INSERT INTO signalisation (`id`, `reference`, `source_id`, `constatateur`, `libelle`, `description`, `instance_id`, `domaine_id`, `type_signalisation_id`, `date_constat`, `date_signale`, `site`, `etat_courant`)
						                     select null, CONCAT('S_', t.id), t.source, t.constateur, t.libelle,t.description,t.perimetre,t.domaine,t.type, STR_TO_DATE(t.date_constat, '%d/%m/%Y'), CURRENT_TIMESTAMP(),t.site,'SIGN_NOUVELLE'
						                     from tmp_signalisation t";
		
		$resultsAction = $this->connection->fetchAll("SELECT id ,source from tmp_signalisation ");
		$query1="INSERT INTO signalisation_has_statut (`id` ,`signalisation_id`,`statut_id`,`dateStatut`,`utilisateur_id`,`commentaire`) values";
		for($i=0; $i<count($resultsAction);$i++) {
			if($i==0){
				$query1.="(null,".($resultsAction[$i]['id']).",".$nouvelle_statut->getId().",NOW(),".$current_user->getId().", 'signalisation importee avec succes')";
			}else{
				$query1.=",(null,".($resultsAction[$i]['id']).",".$nouvelle_statut->getId().",NOW(),".$current_user->getId().", 'signalisation importee avec succes')";
			}
		}
		$query1.=";";
		$this->connection->prepare($query1)->execute();
		$this->connection->prepare($query)->execute();
	}
	
	public function deleteTable() {
		$statement = $this->connection->prepare(sprintf("DROP TABLE IF EXISTS `tmp_signalisation`;"));
		$statement->execute();
	}
	
}