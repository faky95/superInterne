<?php
namespace Orange\MainBundle\Service;

class Extraction extends \PHPExcel {

	public function exportUser($arrData) {
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
								'rgb' => '7FC6BC' 
						) 
				),
				'font' => array(
						'bold' => true,
						'size' => 14,
						'color' => array(
								'rgb' => '000000' 
						) 
				) 
		);
		$data = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER 
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'ffffff' 
						) 
				),
				'font' => array(
						'bold' => false,
						'size' => 11,
						'color' => array(
								'rgb' => '000000' 
						) 
				) 
		);
		$green = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER 
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => '00ff00' 
						) 
				),
				'font' => array(
						'bold' => true,
						'size' => 12,
						'color' => array(
								'rgb' => '000000' 
						) 
				) 
		);
		$red = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER 
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'ff0000' 
						) 
				),
				'font' => array(
						'bold' => true,
						'size' => 12,
						'color' => array(
								'rgb' => '000000' 
						) 
				) 
		);
		
		$objPHPExcel = new \PHPExcel();
		$th = array(
				'Prénom',
				'Nom',
				'Structure',
				'Profil',
				'Etat' 
		);
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
	public function exportAction($arrData, $dataStatut) {
		$arrayStatut = array();
		foreach($dataStatut as $statut) {
			$arrayStatut [$statut->getCode()] = $statut->getLibelle();
		}
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
		$data = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER 
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'ffffff' 
						) 
				),
				'font' => array(
						'size' => 13,
						'color' => array(
								'rgb' => '000000' 
						) 
				) 
		);
		$action = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT 
				) 
		);
		$desc = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT 
				) 
		);
		$objPHPExcel = new \PHPExcel();
		$th = array(
				'Référence',
				'Instance',
				'Libellé',
				'Description',
				'Priorité',
				'Porteur',
				'Direction',
				'Pôle',
				'Département',
				'Service',
				'Type',
				'Statut',
				'Domaine',
				'Contributeurs',
				'Date de début',
				'Date de fin prévue',
				'Date de clôture',
				'Avancements' 
		);
		$col = "A";
		$x = 1;
		foreach($th as $value) {
			if($col == "C") {
				$this->getActiveSheet()->setCellValue($col . $x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col . $x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "D") {
				$this->getActiveSheet()->setCellValue($col . $x, $value)->getColumnDimension($col)->setWidth('50');
				$this->getActiveSheet()->getStyle($col . $x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "R") {
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
		$b = "A";
		foreach($arrData as $value) {
			$b = "A";
			if($b == "A") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getReference())->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "B") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getInstance() ? $value->getInstance()->__toString() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "C") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getLibelle());
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($action)->getAlignment()->setWrapText(true);
				$b ++;
			}
			if($b == "D") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getDescription());
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($desc)->getAlignment()->setWrapText(true);
				$b ++;
			}
			if($b == "E") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getPriorite() ? $value->getPriorite()->__toString() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "F") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getPorteur() ? $value->getPorteur()->getCompletNom() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "G") {
				// $this->getActiveSheet()->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getDirection():' ')->getColumnDimension($b)->setAutoSize(true);
				$this->getActiveSheet()->setCellValue($b.$y, $value->getStructure() ? $value->getStructure()->getArchitectureStructure()->getDirection() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "H") {
				// $this->getActiveSheet()->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getPole():' ')->getColumnDimension($b)->setAutoSize(true);
				$this->getActiveSheet()->setCellValue($b.$y, $value->getStructure() ? $value->getStructure()->getArchitectureStructure()->getPole() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "I") {
				// $this->getActiveSheet()->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getDepartement():' ')->getColumnDimension($b)->setAutoSize(true);
				$this->getActiveSheet()->setCellValue($b.$y, $value->getStructure() ? $value->getStructure()->getArchitectureStructure()->getDepartement() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "J") {
				// $this->getActiveSheet()->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getService():' ')->getColumnDimension($b)->setAutoSize(true);
				$this->getActiveSheet()->setCellValue($b.$y, $value->getStructure() ? $value->getStructure()->getArchitectureStructure()->getService() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "K") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getTypeAction() ? $value->getTypeAction()->__toString() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "L") {
				$this->getActiveSheet()->setCellValue($b.$y, $arrayStatut [$value->getEtatReel()])->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "M") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getDomaine() ? $value->getDomaine()->__toString() : ' ')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "N") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getAllContributeur())->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "O") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getDateDebut()->format('d-m-Y'))->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "P") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getDateInitial()->format('d-m-Y'))->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "Q") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getDateFinExecut() ? $value->getDateFinExecut()->format('d-m-Y') : 'En Cours')->getColumnDimension($b)->setAutoSize(true);
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			if($b == "R") {
				$this->getActiveSheet()->setCellValue($b.$y, $value->getAllAvancement());
				// $this->getActiveSheet()->getStyle($b.$y)->applyFromArray($data);
				$b ++;
			}
			$y ++;
		}
		$this->getActiveSheet()->getStyle('C2:C' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('D2:D' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('R2:R' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('N2:N' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
		$this->getActiveSheet()->getStyle('A2:A' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('E2:E' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('F2:F' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('G2:G' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('H2:H' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('I2:I' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('J2:J' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('K2:K' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('L2:L' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('M2:M' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('N2:N' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('O2:O' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('P2:P' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('Q2:Q' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('R2:R' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('B2:B' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('C2:C' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
		$this->getActiveSheet()->getStyle('D2:D' . $this->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);
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
				'Source', 'Date de signalisation', 'Direction', 'Pôle', 'Département', 'Service', 'Statut', 'Action(s)' 
			);
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
				if($b == "B") {
					$inst = explode("##", $value);
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
		$objWriter->setEnclosure('');
		$objWriter->setLineEnding("\r\n");
		return $objWriter;
	}
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
		$data = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER 
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array(
								'rgb' => 'ffffff' 
						) 
				),
				'font' => array(
						'size' => 13,
						'color' => array(
								'rgb' => '000000' 
						) 
				) 
		);
		$action = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT 
				) 
		);
		$desc = array(
				'borders' => array(
						'top' => $default_border,
						'bottom' => $default_border,
						'left' => $default_border,
						'right' => $default_border 
				),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT 
				) 
		);
		$objPHPExcel = new \PHPExcel();
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
	