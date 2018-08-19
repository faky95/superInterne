<?php
namespace Orange\MainBundle\Service;

use Orange\MainBundle\Entity\Statut;

class Extraction extends \PHPExcel {

	/**
	 * @param array $arrData
	 * @return \PHPExcel_Writer_Excel2007
	 */
	public function exportUser($arrData) {
		
		$default_border = array(
				'style' => \PHPExcel_Style_Border::BORDER_THIN, 'size' => 16, 'color' => array('rgb' => '000000') 
			);
		$style_th = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '7FC6BC')),
				'font' => array('bold' => true, 'size' => 14, 'color' => array('rgb' => '000000'))
			);
		$data = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ffffff')),
				'font' => array('bold' => false, 'size' => 11, 'color' => array('rgb' => '000000'))
			);
		$green = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '00ff00')),
				'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => '000000'))
			);
		$red = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER, 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ff0000')),
				'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => '000000'))
			);
		$th = array('Prénom', 'Nom', 'Structure', 'Profil', 'Etat');
		$col = "A";
		$x = 1;
		foreach($th as $value) {
			$this->getActiveSheet()->setCellValue($col . $x, $value)->getColumnDimension($col)->setAutoSize(true);
			$this->getActiveSheet()->getStyle($col . $x)->applyFromArray($style_th);
			$col ++;
		}
		$y = 2;
		foreach($arrData as $values) {
			$b = "A";
			foreach($values as $value) {
				if($b == "D") {
					$this->getActiveSheet()->setCellValue($b.$y, $value);
					$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data)->getAlignment()->setWrapText(true);
					$b ++;
				} elseif($b == "E") {
					if($value == true) {
						$this->getActiveSheet()->setCellValue($b.$y, "Actif");
						$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($green);
					} else {
						$this->getActiveSheet()->setCellValue($b.$y, "Inactif");
						$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($red);
					}
					$b ++;
				} else {
					$this->getActiveSheet()->setCellValue($b.$y, $value);
					$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
			}
			$y ++;
		}
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
	/**
	 * @param array $arrData
	 * @param array $dataStatut
	 * @return \PHPExcel_Writer_Excel2007
	 */
	public function exportAction($arrData, $dataStatut) {
		$arrayStatut = array();
		foreach($dataStatut as $statut) {
			$arrayStatut [$statut->getCode()] = $statut->getLibelle();
		}
		$default_border = array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'size' => 16, 'color' => array('rgb' => '000000'));
		$style_th = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ff6600')),
				'font' => array('bold' => true, 'size' => 16, 'color' => array('rgb' => '000000')) 
			);
		$th = array(
				'Référence', 'Instance', 'Libellé', 'Description', 'Priorité', 'Porteur', 'Direction', 'Pôle', 'Département', 'Service', 'Type', 
				'Statut', 'Domaine', 'Contributeurs', 'Date de début', 'Délai initial', 'Date de fin prévue', 'Date de clôture', 'Respect du délai', 'Avancements' 
			);
		$col = "A";
		$x = 1;
		foreach($th as $value) {
			if($col == "C") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "D") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "R") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('40');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} else {
				$this->getActiveSheet()->setCellValue($col . $x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			}
		}
		$tableau = array();
		foreach($arrData as $val) {
			$value = $val[0];
			$dateFin = $value['dateFinExecut'] ? $value['dateFinExecut'] : $value['dateCloture'];
			$tableau[] = array(
					$value['reference'],
					$value['instance']['libelle'],
					$value['libelle'],
					$value['description'],
					$value['priorite']['libelle'],
					$value['porteur']['prenom'].' '.$value['porteur']['nom'],
					$value['structure']['direction'],
					$value['structure']['pole'],
					$value['structure']['departement'],
					$value['structure']['service'],
					$value['typeAction']['type'],
					$arrayStatut [$value['etatReel']],
					$value['domaine']['libelleDomaine'],
					$val['contributeurs'],
					$value['dateDebut'] ? $value['dateDebut']->format('d-m-Y') : '',
					$value['dateInitial'] ? $value['dateInitial']->format('d-m-Y') : '',
					$value['dateFinPrevue'] ? $value['dateFinPrevue']->format('d-m-Y') : '',
					$dateFin ? $dateFin->format('d-m-Y') : 'En Cours',
					$this->respectDelai($value),
					$val['avancements']
				);
		}
		$this->getActiveSheet()->fromArray($tableau, '', 'A2');
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
	/**
	 * @param array $data
	 * @return number
	 */
	private function respectDelai($data) {
		$value = null;
		if(in_array($data['etatReel'], array(Statut::ACTION_SOLDEE_DELAI, Statut::ACTION_SOLDEE_HORS_DELAI))==false) {
			$value = null;
		} elseif($data['dateFinExecut']==null) {
			$value = 'NA';
		} elseif($data['dateDebut']==$data['dateInitial']) {
			$value = $data['dateDebut']==$data['dateFinExecut'] ? 100 : 0;
		} else {
			$value = 100 * number_format($data['dateDebut']->diff($data['dateInitial'])->format('%R%a') / $data['dateDebut']->diff($data['dateFinExecut'])->format('%R%a'), 1);
		}
		return $value;
	}
	
	/**
	 * @param array $arrData
	 * @return \PHPExcel_Writer_Excel2007
	 */
	public function exportNotification($arrData) {
		$th = array('Libellé', 'Destinataire', 'Copie', 'Date');
		$this->getActiveSheet()->fromArray($th);
		$tableau = array();
		$this->getActiveSheet()->getColumnDimension('A')->setWidth('25');
		$this->getActiveSheet()->getColumnDimension('B')->setWidth('50');
		$this->getActiveSheet()->getColumnDimension('C')->setWidth('50');
		$this->getActiveSheet()->getColumnDimension('D')->setWidth('25');
		foreach($arrData as $val) {
			$tableau[] = array(
					$val->getTypeNotification()->getLibelle(),
					$val->getDestinataireForReporting(),
					$val->getCopyForReporting(),
					$val->getDate() ? $val->getDate()->format('d-m-Y') : ''
				);
		}
		$this->getActiveSheet()->fromArray($tableau, '', 'A2');
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
	/**
	 * @param array $arrData
	 * @param array $dataStatut
	 * @return \PHPExcel_Writer_Excel2007
	 */
	public function exportActionCyclique($arrData, $dataStatut) {
		$arrayStatut = array();
		foreach($dataStatut as $statut) {
			$arrayStatut [$statut->getCode()] = $statut->getLibelle();
		}
		$default_border = array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'size' => 16, 'color' => array('rgb' => '000000'));
		$style_th = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ff6600')),
				'font' => array('bold' => true, 'size' => 16, 'color' => array('rgb' => '000000'))
			);
		$th = array(
				'Référence', 'Periodicite','Delai des occurences dans le mois ', 'Delai des occurences dans la semaine','Instance', 'Libellé', 'Description', 'Priorité', 
				'Porteur', 'Direction', 'Pôle', 'Département', 'Service', 'Type', 'Statut', 'Domaine', 'Contributeurs', 'Date de début', 'Date de fin prévue', 'Date de clôture'
			);
		$col = "A";
		$x = 1;
		foreach($th as $value) {
			if($col == "E") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "F") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "S") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} else {
				$this->getActiveSheet()->setCellValue($col . $x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			}
		}
		$tableau = array();
		$i=0;
		foreach($arrData as $value) {
			$tableau[] = array(
					$value[0]['action']['reference'],
					$value[0]['pas']['libelle'],
					$value[0]['dayOfMonth']['libelle'],
					$value[0]['dayOfWeek']['libelle'],
					$value[0]['action']['instance']['libelle'],
					$value[0]['action']['libelle'],
					$value[0]['action']['description'],
					$value[0]['action']['priorite']['libelle'],
					$value[0]['action']['porteur']['prenom'].' '.$value[0]['action']['porteur']['nom'],
					$value[0]['action']['structure']['direction'],
					$value[0]['action']['structure']['pole'],
					$value[0]['action']['structure']['departement'],
					$value[0]['action']['structure']['service'],
					$value[0]['action']['typeAction']['type'],
					$arrayStatut [$value[0]['action']['etatReel']],
					$value[0]['action']['domaine']['libelleDomaine'],
					$value['contributeurs'],
					$value[0]['action']['dateDebut'] ? $value[0]['action']['dateDebut']->format('d-m-Y') : '',
					$value[0]['action']['dateInitial'] ? $value[0]['action']['dateInitial']->format('d-m-Y') : '',
					$value[0]['action']['dateFinExecut'] ? $value[0]['action']['dateFinExecut']->format('d-m-Y') : 'En Cours',
			);
			$i++;
		}
		$this->getActiveSheet()->fromArray($tableau, '', 'A2');
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
	/**
	 * @param array $arrData
	 * @param array $dataStatut
	 * @return \PHPExcel_Writer_Excel2007
	 */
	public function exportOccurence($arrData, $dataStatut) {
		$arrayStatut = array();
		foreach($dataStatut as $statut) {
			$arrayStatut [$statut->getCode()] = $statut->getLibelle();
		}
		$default_border = array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'size' => 16, 'color' => array('rgb' => '000000'));
		$style_th = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ff6600')),
				'font' => array('bold' => true, 'size' => 16, 'color' => array('rgb' => '000000'))
		);
		$th = array(
				'Référence', 'Periodicite','Delai des occurences dans le mois ', 'Delai des occurences dans la semaine','Instance', 'Libellé', 'Description', 
				'Priorité', 'Porteur', 'Direction', 'Pôle', 'Département', 'Service', 'Type', 'Domaine', 'Date de début', 'Date de fin prévue', 'Date de clôture', 'Statut'
		);
		$col = "A";
		$x = 1;
		foreach($th as $value) {
			if($col == "E") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "F") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "S") {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} else {
				$this->getActiveSheet()->setCellValue($col . $x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			}
		}
		$tableau = array();
		$i=0;
		foreach($arrData as $value) {
			$action = $value['actionCyclique']['action'];
			$tableau[] = array(
					$value['reference'],
					$value['actionCyclique']['pas']['libelle'],
					$value['actionCyclique']['dayOfMonth']['libelle'],
					$value['actionCyclique']['dayOfWeek']['libelle'],
					$action['instance']['libelle'],
					$action['libelle'],
					$action['description'],
					$action['priorite']['libelle'],
					$action['porteur']['prenom'].' '.$action['porteur']['nom'],
					$action['structure']['direction'],
					$action['structure']['pole'],
					$action['structure']['departement'],
					$action['structure']['service'],
					$action['typeAction']['type'],
					$action['domaine']['libelleDomaine'],
					$value['dateDebut'] ? $value['dateDebut']->format('d-m-Y') : '',
					$value['dateInitial'] ? $value['dateInitial']->format('d-m-Y') : '',
					$value['dateFinExecut'] ? $value['dateFinExecut']->format('d-m-Y') : 'En Cours',
					$arrayStatut[$value['etatCourant']]
				);
			$i++;
		}
		$this->getActiveSheet()->fromArray($tableau, '', 'A2');
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
	public function exportSignalisation($arrData) {
		$default_border = array(
				'style' => \PHPExcel_Style_Border::BORDER_THIN, 'size' => 20, 'color' => array('rgb' => '000000'));
		$style_th = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ffff00')),
				'font' => array('bold' => true, 'size' => 16, 'color' => array('rgb' => '000000')) 
			);
		$data = array(
				'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
				'alignment' => array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ffffff')),
				'font' => array('size' => 12, 'color' => array('rgb' => '000000')) 
			);
		$th = array(
				'Référence Signalisation', 'Instance', 'Périmétre', 'Domaine', 'Type', 'Libellé', 'Description',
				'Source', 'Date de signalisation', 'Direction', 'Pôle', 'Département', 'Service', 'Statut', 'Action(s)', 'Motif d\'invalidation' 
			);
		$col = "A";
		$x = 1;
		foreach($th as $value) {
			$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
			$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_th);
			$col++;
		}
		$y = 2;
		foreach($arrData as $values) {
			$b = "A";
			foreach($values as $value) {
				if($b == "B") {
					$inst = explode('##', $value);
					$this->getActiveSheet()->setCellValue($b.$y, $inst[0])->getColumnDimension($b)->setAutoSize(true);
					$style_instance = array(
							'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
							'alignment' => array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
							'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $inst [1])),
							'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'ffffff')) 
						);
					$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($style_instance);
					$b ++;
				} elseif($b == "C") {
					$inst = explode("##", $value);
					$this->getActiveSheet()->setCellValue($b.$y, $inst[0])->getColumnDimension($b)->setAutoSize(true);
					$style_instance = array(
							'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
							'alignment' => array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
							'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $inst[1])),
							'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'ffffff')) 
						);
					$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($style_instance);
					$b ++;
				} elseif($b == "E") {
					$inst = explode("##", $value);
					$this->getActiveSheet()->setCellValue($b.$y, $inst[0])->getColumnDimension($b)->setAutoSize(true);
					$style_instance = array(
							'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
							'alignment' => array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
							'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => $inst[1])),
							'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'ffffff')) 
						);
					$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($style_instance);
					$b ++;
				} elseif($b == "G") {
					$this->getActiveSheet()->setCellValue($b.$y, $value);
					$cont = array(
							'borders' => array('top' => $default_border, 'bottom' => $default_border, 'left' => $default_border, 'right' => $default_border),
							'alignment' => array('vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP, 'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
						);
					$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($cont)->getAlignment()->setWrapText(true);
					$b ++;
				} else {
					$this->getActiveSheet()->setCellValue($b.$y, $value)->getColumnDimension($b)->setAutoSize(true);
					$this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
			}
			$y ++;
		}
		$this->getActiveSheet()->getStyle('B2:B' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
	public function exportCanevas($arrData) {
		$th = array(
				'Réference', 'Prénom et Nom du porteur', 'Email porteur', 'Instance', 'Email(s) contributeur(s)', 'Statut', 'Type',
				'Domaine', 'Date de début', 'Délai initial', 'Date de clôture', 'Libellé action', 'Description action', 'Priorité' 
			);
		$col = "A";
		$x = 1;
		foreach(array_map("utf8_decode", $th) as $value) {
			$this->getActiveSheet()->setCellValueExplicit($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
			$col ++;
		}
		$y = 2;
		foreach($arrData as $values) {
			$b = "A";
			foreach(array_map("utf8_decode", $values) as $value) {
				$this->getActiveSheet()->setCellValueExplicit($b.$y, $value)->getColumnDimension($b)->setAutoSize(true);
				$b ++;
			}
			$y ++;
		}
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'CSV');
		$objWriter->setDelimiter(';');
		$objWriter->setEnclosure('"');
		$objWriter->setLineEnding("\r\n");
		return $objWriter;
	}
	
	/**
	 * @param array $arrData
	 * @return \PHPExcel_Writer_Excel2007
	 */
	public function exportInstance($arrData) {
		$default_border = array(
				'style' => \PHPExcel_Style_Border::BORDER_THIN,
				'size' => 16,
				'color' => array(
						'rgb' => '000000' 
				) 
		);
		$style_th = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER 
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'ff6600' 
						) 
				),
				'font' => array(
						'bold' => true,
						'size' => 16,
						'color' => array(
								'rgb' => '000000' 
						) 
				) 
		);
		$th = array(
				'Instance',
				'Type d\'insatnce',
				'Description',
				'Animateurs',
				'Domaines',
				'Types',
				'Instance parente' 
		);
		$col = "A";
		$x = 1;
		foreach($th as $value) {
			if($col == "C") {
				$this->getActiveSheet()->setCellValue($col . $x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col . $x)->applyFromArray($style_th);
				$col ++;
			} else {
				$this->getActiveSheet()->setCellValue($col . $x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col . $x)->applyFromArray($style_th);
				$col ++;
			}
		}
		$y = 2;
		foreach($arrData as $value) {
			$b = "A";
			if($b == "A") {
				$this->getActiveSheet()->setCellValue($b.$y, $value ['libelle'])->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "B") {
				$this->getActiveSheet()->setCellValue($b.$y, $value ['type_instance'])->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "C") {
				$this->getActiveSheet()->setCellValue($b.$y, $value ['description']);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "D") {
				$this->getActiveSheet()->setCellValue($b.$y, $value ['animateur'])->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "E") {
				$this->getActiveSheet()->setCellValue($b.$y, $value ['domaine'])->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "F") {
				$this->getActiveSheet()->setCellValue($b.$y, $value ['type'])->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "F") {
				$this->getActiveSheet()->setCellValue($b.$y, $value ['parent'])->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			$y ++;
		}
		$this->getActiveSheet()->getStyle('C2:C' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('D2:D' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('E2:E' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('F2:F' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		
		// $this->getActiveSheet()->getStyle('C2:C'.$this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
}
	