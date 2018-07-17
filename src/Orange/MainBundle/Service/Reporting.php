<?php
namespace Orange\MainBundle\Service;

use Orange\QuickMakingBundle\Service\PHPExcelAdvanced;

class Reporting extends PHPExcelAdvanced {

	public function reportingInstanceAction($arrData, $statut, $actions, $statuts, $withPorteur = false) {
		$this->removeSheetByIndex(0);
		$this->createSheet(0);
		$this->setActiveSheetIndex(0);
		if($this->getSheetCount() > 1) {
			for($i = $this->getSheetCount()-1; $i > 0 ; $i--) {
				$this->removeSheetByIndex($i);
			}
		}
		$sheet2 = $this->createSheet(1);
		$sheet3 = $this->createSheet(2);

		$x = 1;
		$col = 'B';
		$style_instance = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ffc000')),
				'font' => array('bold' => true, 'size' => 18, 'color' => array('rgb' => '000000'))
			);
		$style_stat = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'font' => array('bold' => true, 'size' => 14, 'color' => array('rgb' => '000000'))
			);
		$style_inst = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'font' => array('bold' => true, 'size' => 12, 'color' => array('rgb' => 'ffc000'))
			);
		$style_valeur = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'font' => array('bold' => false, 'size' => 10, 'color' => array('rgb' => '000000'))
			);
		$style_valeur_total = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'font' => array('bold' => true, 'size' => 14, 'color' => array('rgb' => '000000'))
			);
		$style_libelle_total = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
				'font' => array('bold' => true, 'size' => 14, 'color' => array('rgb' => '000000'))
			);
		$style_taux = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'font' => array('bold' => true, 'size' => 14, 'color' => array('rgb' => 'ff6600'))
			);
		$style_libelle_taux = array('font' => array('bold' => true, 'size' => 14, 'color' => array('rgb' => 'ff6600')));
		$this->getActiveSheet()->setCellValue('A2', "Statut")->getColumnDimension('A')->setAutoSize(true);
		$this->getActiveSheet()->getStyle('A2')->applyFromArray($style_stat);
		$size = sizeof($arrData ['instance']);
		$n = \PHPExcel_Cell::columnIndexFromString($col);
		$finCol = \PHPExcel_Cell::stringFromColumnIndex(($n - 2) + $size);
		$this->getActiveSheet()->setCellValue($col.$x, "Instances")->getColumnDimension($col)->setAutoSize(true);
		$this->getActiveSheet()->mergeCells($col.$x.':'.$finCol.$x)->getStyle($col.$x)->applyFromArray($style_instance);
		$col = 'B';
		$y = 2;
		foreach($arrData ['instance'] as $value) {
			$this->getActiveSheet()->setCellValue($col.$y, $value ['libelle'])->getColumnDimension($col)->setAutoSize(true);
			$this->getActiveSheet()->getStyle($col.$y)->applyFromArray($style_inst);
			$col ++;
		}
		$col = 'A';
		$x = 3;
		$i = 1;
		foreach($statut as $key => $value) {
			if($i < 9) {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$col2 = 'B';
				foreach($arrData ['instance'] as $valeur) {
					$this->getActiveSheet()->setCellValue($col2.$x, $valeur['data'] [$key]);
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_valeur);
					$col2 ++;
				}
			} elseif($i == 9) {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_libelle_total);
				$col2 = 'B';
				foreach($arrData ['instance'] as $valeur) {
					$this->getActiveSheet()->setCellValue($col2.$x, $valeur['data'][$key]);
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_valeur_total);
					$col2 ++;
				}
			} else {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_libelle_taux);
				$col2 = 'B';
				foreach($arrData ['instance'] as $valeur) {
					$this->getActiveSheet()->setCellValue($col2.$x, $valeur['data'][$key].'%');
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_taux);
					$col2 ++;
				}
			}
			$x ++;
			$i ++;
		}
		$sheet2 = Reporting::exportAction($sheet2, $actions, $statuts);
		$sheet3 = Reporting::syntheseInstance($sheet3, $arrData, $statut, $statuts);
		if($withPorteur) {
			$sheet4 = $this->createSheet(3);
			$sheet4 = Reporting::synthesePorteur($sheet4, $arrData, $statut, $statuts);
			$sheet4->setTitle('Synthèse par porteur');
		}
		$this->setActiveSheetIndex(0);
		$this->getActiveSheet()->setTitle('stats_par_instance');
		$sheet2->setTitle('actions');
		$sheet3->setTitle('Synthèse par instance');
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}

	public function reportingStructureAction($arrData, $statut, $actions, $statuts) {
		$this->removeSheetByIndex(0);
		$this->createSheet(0);
		$this->setActiveSheetIndex(0);
		if($this->getSheetCount() > 1) {
			for($i = $this->getSheetCount()-1; $i > 0 ; $i--)
				$this->removeSheetByIndex($i);
		}
		$sheet2 = $this->createSheet(1);
		$x = 1;
		$col = 'B';
		$style_instance = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array(		'rgb' => 'ffc000' )
				),
				'font' => array('bold' => true,'size' => 18,'color' => array(		'rgb' => '000000' )
				)
		);
		$style_stat = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'font' => array('bold' => true,'size' => 14,'color' => array(		'rgb' => '000000' )
				)
		);
		$style_inst = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'font' => array('bold' => true,'size' => 12,'color' => array(		'rgb' => 'ffc000' )
				)
		);
		$style_valeur = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'font' => array('bold' => true,'size' => 12,'color' => array(		'rgb' => '000000' )
				)
		);
		$style_taux = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
				),
				'font' => array('bold' => true,'size' => 14,'color' => array(		'rgb' => 'ff6600' )
				)
		);
		$style_libelle_taux = array(
				'font' => array('bold' => true,'size' => 14,'color' => array(		'rgb' => 'ff6600' )
				)
		);
		$this->getActiveSheet()->setCellValue('A2', "Statut")->getColumnDimension('A')->setAutoSize(true);
		$this->getActiveSheet()->getStyle('A2')->applyFromArray($style_stat);
		$size = sizeof($arrData ['structure']);
		$n = \PHPExcel_Cell::columnIndexFromString($col);
		$finCol = \PHPExcel_Cell::stringFromColumnIndex(($n - 2) + $size);
		$this->getActiveSheet()->setCellValue($col.$x, "Structures")->getColumnDimension($col)->setAutoSize(true);
		$this->getActiveSheet()->mergeCells($col.$x.':'.$finCol.$x)->getStyle($col.$x)->applyFromArray($style_instance);
		$col = 'B';
		$y = 2;
		foreach($arrData ['structure'] as $value) {
			$this->getActiveSheet()->setCellValue($col.$y, $value ['libelle'])->getColumnDimension($col)->setAutoSize(true);
			$this->getActiveSheet()->getStyle($col.$y)->applyFromArray($style_inst);
			$col ++;
		}
		$col = 'A';
		$x = 3;
		$i = 1;
		foreach($statut as $key => $value) {
			if($i <= 9) {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$col2 = 'B';
				foreach($arrData ['structure'] as $value) {
					$this->getActiveSheet()->setCellValue($col2.$x, $value ['data'] [$key]);
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_valeur);
					$col2 ++;
				}
			} else {
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_libelle_taux);
				$col2 = 'B';
				foreach($arrData ['structure'] as $value) {
					$this->getActiveSheet()->setCellValue($col2.$x, $value ['data'] [$key].'%');
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_taux);
					$col2 ++;
				}
			}
				
			$x ++;
			$i ++;
		}
		$sheet2 = Reporting::exportAction($sheet2, $actions, $statuts);
		$this->setActiveSheetIndex(0);
		$this->getActiveSheet()->setTitle('stats_par_structure');
		$sheet2->setTitle('actions');

		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}

	public function exportAction($sheet, $arrData, $dataStatut) {
		$arrayStatut = array();
		foreach($dataStatut as $statut) {
			$arrayStatut [$statut->getCode()] = $statut->getLibelle();
		}
		$default_border = array(
				'style' => \PHPExcel_Style_Border::BORDER_THIN, 'size' => 16, 'color' => array('rgb' => '000000')
			);
		$style_th = array(
				'borders' => array('top' => $default_border,'bottom' => $default_border,'left' => $default_border,'right' => $default_border),
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => 'ff6600')),
				'font' => array('bold' => true,'size' => 16,'color' => array('rgb' => '000000')
			));
		$objPHPExcel = new \PHPExcel();
		$th = array(
				'Référence', 'Instance', 'Libellé', 'Description', 'Priorité', 'Porteur', 'Direction', 'Pôle', 'Département',
				'Service', 'Type', 'Statut', 'Domaine', 'Contributeurs', 'Date de début', 'Date de fin prévue', 'Date de clôture', 'Avancements'
			);
		$col = "A";
		$x = 1;
		foreach($th as $value) {
			if($col == "C") {
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$sheet->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "D") {
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$sheet->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} elseif($col == "R") {
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$sheet->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			} else {
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$sheet->getStyle($col.$x)->applyFromArray($style_th);
				$col ++;
			}
		}
		$y = 2;
		$b = "A";
		if($arrData) {
			foreach($arrData as $value) {
				$b = "A";
				if($b == "A") {
					$sheet->setCellValue($b.$y, $value->getReference())->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "B") {
					$sheet->setCellValue($b.$y, $value->getInstance() ? $value->getInstance()->__toString() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "C") {
					$sheet->setCellValue($b.$y, $value->getLibelle());
					// $sheet->getStyle($b.$y)->applyFromArray($action)->getAlignment()->setWrapText(true);
					$b ++;
				}
				if($b == "D") {
					$sheet->setCellValue($b.$y, $value->getDescription());
					// $sheet->getStyle($b.$y)->applyFromArray($desc)->getAlignment()->setWrapText(true);
					$b ++;
				}
				if($b == "E") {
					$sheet->setCellValue($b.$y, $value->getPriorite() ? $value->getPriorite()->__toString() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "F") {
					$sheet->setCellValue($b.$y, $value->getPorteur() ? $value->getPorteur()->getCompletNom() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "G") {
					// $sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getDirection():' ')->getColumnDimension($b)->setAutoSize(true);
					$sheet->setCellValue($b.$y, $value->getStructure() ? $value->getStructure()->getDirection() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "H") {
					// $sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getPole():' ')->getColumnDimension($b)->setAutoSize(true);
					$sheet->setCellValue($b.$y, $value->getStructure() ? $value->getStructure()->getPole() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "I") {
					// $sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getDepartement():' ')->getColumnDimension($b)->setAutoSize(true);
					$sheet->setCellValue($b.$y, $value->getStructure() ? $value->getStructure()->getDepartement() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "J") {
					// $sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getService():' ')->getColumnDimension($b)->setAutoSize(true);
					$sheet->setCellValue($b.$y, $value->getStructure() ? $value->getStructure()->getService() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "K") {
					$sheet->setCellValue($b.$y, $value->getTypeAction() ? $value->getTypeAction()->__toString() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "L") {
					$sheet->setCellValue($b.$y, $arrayStatut [$value->getEtatReel()])->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "M") {
					$sheet->setCellValue($b.$y, $value->getDomaine() ? $value->getDomaine()->__toString() : ' ')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "N") {
					$sheet->setCellValue($b.$y, $value->getAllContributeur())->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "O") {
					$sheet->setCellValue($b.$y, $value->getDateDebut()->format('d-m-Y'))->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "P") {
					$sheet->setCellValue($b.$y, $value->getDateInitial()->format('d-m-Y'))->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}
				if($b == "Q") {
					$sheet->setCellValue($b.$y, $value->getDateFinExecut() ? $value->getDateFinExecut()->format('d-m-Y') : 'En Cours')->getColumnDimension($b)->setAutoSize(true);
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				if($b == "R") {
					$sheet->setCellValue($b.$y, $value->getAllAvancement());
					// $sheet->getStyle($b.$y)->applyFromArray($data);
					$b ++;
				}
				$y ++;
			}
		}
		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
		return $sheet;
	}

	public function synthesePorteur($sheet, $arrData, $statut, $statuts) {
		$this->setActiveSheetIndex(3);
		$row = 1;
		$this->writeSyntheseByPorteur($sheet, $arrData['porteurs'], $statut, $statuts, 'Globale', $row, 'FF6600');
		$row += 2;
		foreach($arrData['instance'] as $data) {
			if(!isset($data['porteurs'])) {
				continue;
			}
			$this->writeSyntheseByPorteur($sheet, $data['porteurs'], $statut, $statuts, $data['libelle'], $row, substr($data['couleur'], 1));
			$row += 2;
		}
		return $sheet;
	}

	/**
	 *
	 * @param PHPExcel_Worksheet $sheet
	 * @param array $arrData
	 * @param array $statut
	 * @param array $statuts
	 * @param string $title
	 * @param string $row
	 */
	private function writeSyntheseByPorteur($sheet, $arrData, $statut, $statuts, $title, &$row, $couleur) {
		$debut = $row;
		$total = array(
				'nbAbandon'=>0, 'nbDemandeAbandon'=>0, 'nbFaiteDelai'=>0, 'nbFaiteHorsDelai'=>0, 'nbNonEchue'=>0, 'nbActionNouvelle'=>0,
				'nbEchueNonSoldee'=>0, 'nbSoldeeHorsDelais'=>0, 'nbSoldeeDansLesDelais'=>0, 'totalSoldee'=>0, 'total'=>0
		);
		$columns = array(
				'B'=>'total', 'C'=>'nbSoldeeHorsDelais' , 'E'=>'nbSoldeeDansLesDelais', 'I'=>'nbAbandon', 'K'=>'nbDemandeAbandon', 'M'=>'nbFaiteDelai', 'O'=>'nbFaiteHorsDelai', 'Q'=>'nbNonEchue',
				'S'=>'nbEchueNonSoldee',
		);
		$tColumns = array('D'=>'tSHD','F'=>'tSDD', 'H'=>'tSolde', 'J'=>'tAbandon', 'L'=>'tDA', 'N'=>'tFD','P'=>'tFHD' , 'R'=>'tNE', 'T'=>'tENS');
		$this->setColumnWidth(array('A' => 30, 'L' => 20, 'T' => 20));
		$this->mergeCells(array(
				array('C'.$row, 'D'.$row), array('E'.$row, 'F'.$row), array('G'.$row, 'H'.$row), array('I'.$row, 'J'.$row), array('K'.$row, 'L'.$row),  array('M'.$row, 'N'.$row),array('O'.$row, 'P'.$row)
				, array('Q'.$row, 'R'.$row), array('S'.$row, 'T'.$row)
		));
		$sheet->setCellValue('A'.$row, sprintf('Synthèse %s par Porteur', $title));
		$sheet->setCellValue('G'.$row, 'Total soldé');
		$this->addRowData($statut, $columns, $row);
		$row++;
		foreach($arrData as $data) {
			$data['totalSoldee'] = $data['nbSoldeeHorsDelais'] + $data['nbSoldeeDansLesDelais'];
			foreach(array_merge(array('G'=>'totalSoldee'), $columns) as $etat) {
				$total[$etat] += $data[$etat];
			}
			$data = $this->addPercent($data, array(
					'tAbandon'=>'nbAbandon', 'tDA'=>'nbDemandeAbandon', 'tFD'=>'nbFaiteDelai','tFHD'=>'nbFaiteHorsDelai','tNE'=>'nbNonEchue', 'tENS'=>'nbEchueNonSoldee', 'tSHD'=>'nbSoldeeHorsDelais','tSDD'=>'nbSoldeeDansLesDelais' ,'tSolde'=>'totalSoldee',
			), 'total');
			$this->addRowData($data, array_merge(array('A' => 'libelle', 'G'=>'totalSoldee'), array_merge($columns, $tColumns)), $row);
			$row++;
		}
		$total = $this->addPercent($total, array(
				'tAbandon'=>'nbAbandon', 'tDA'=>'nbDemandeAbandon', 'tFD'=>'nbFaiteDelai','tFHD'=>'nbFaiteHorsDelai','tNE'=>'nbNonEchue', 'tENS'=>'nbEchueNonSoldee', 'tSHD'=>'nbSoldeeHorsDelais','tSDD'=>'nbSoldeeDansLesDelais' ,'tSolde'=>'totalSoldee',
		), 'total');
		$sheet->setCellValue('A'.$row, 'Total des actions');
		$this->addRowData($total, array_merge(array('G'=>'totalSoldee'), array_merge($columns, $tColumns)), $row);
		$this->applyStyleRanges(array(
				"A$debut:T$debut", "B$debut:B$row", "C$debut:D$row", "E$debut:F$row" ,"G$debut:H$row", "I$debut:J$row", "K$debut:L$row","M$debut:N$row", "O$debut:P$row", "Q$debut:R$row",
				"A$debut:A$row", "S$debut:T$row"
		), array(
				'outline_border' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM) ,
		));
		$this->applyStyleRanges(array(
				"B$debut:T$debut", "B$debut:B$row", "C$debut:D$row", "E$debut:F$row" ,"G$debut:H$row", "I$debut:J$row", "K$debut:L$row","M$debut:N$row", "O$debut:P$row", "Q$debut:R$row",
				 "S$debut:T$row"
		), array(
				'text_align' => array('horizontal' =>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		));
		$this->applyStyleRanges(array(sprintf("G%s:H%s", $debut+1, $row)), array('bgcolor' => 'D8FFFF'));
		$this->applyStyleRanges(array("A$debut:A$row", "A$debut:T$debut", "B$debut:B$row"), array('bgcolor' => $couleur));
		$row++;
	}

	public function syntheseInstance($sheet, $arrData, $statut, $statuts) {
		$x = 1;
		$col = 'A';
		$style_entete_instance_merge = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'ffc000')),
				'font' => array('bold' => true, 'size' => 13, 'color' => array('rgb' => '000000'))
		);
		$style_instances = array(
				'font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => 'ffc000')),
				'borders' => array(
						'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM),
						'top' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM)
				));
		$style_instances_taux = array(
				'font' => array('bold' => true, 'size' => 11, 'color' => array('rgb' => 'ffc000')),
				'borders' => array(
						'right' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM),
						'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM),
						'top' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM)
				));
		$style_entete_total = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '000000')),
				'font' => array('bold' => true,'size' => 13,'color' => array('rgb' => 'ffffff')),
				'borders' => array('horizontal' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM))
		);
		$style_bold = array(
				'font' => array('bold' => true,'size' => 11,'color' => array('rgb' => '000000')),
				'borders' => array(
						'bottom' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM),
						'top' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM),
						'right' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM)
				));
		$style_tot = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'font' => array('bold' => true,'size' => 11,'color' => array('rgb' => '000000')),
				'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM))
		);
		$style_solde = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '88cc00')),
				'font' => array('bold' => true,'size' => 11,'color' => array('rgb' => '000000'))
		);
		$style_solde_valeur = array_merge($style_solde, array(
				'borders' => array('left' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM))
		));
		$style_solde_taux = array_merge($style_solde, array(
				'borders' => array('right' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM))
		));
		$style_body_valeur = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'borders' => array('left' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM))
		);
		$style_body_taux = array(
				'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
				'borders' => array('right' => array('style' => \PHPExcel_Style_Border::BORDER_MEDIUM))
		);
		$sheet->setCellValue('A2', "Statut")->getColumnDimension('A')->setAutoSize(true);
		$size = (sizeof($arrData ['instance'])*2)+1;
		$n = \PHPExcel_Cell::columnIndexFromString($col);
		$finCol = \PHPExcel_Cell::stringFromColumnIndex(($n - 2) + $size);
		$sheet->setCellValue($col.$x, "Instances")->getColumnDimension($col)->setAutoSize(true);
		$sheet->mergeCells($col.$x.':'.$finCol.$x)->getStyle()->applyFromArray($style_entete_instance_merge);
		$finCol++;
		$deb =$finCol++;
		$fin =$finCol++;
		$sheet->getStyle('A2')->applyFromArray($style_bold);
		$sheet->setCellValue($deb.$x, "Total")->getColumnDimension($deb)->setAutoSize(true);
		$sheet->mergeCells($deb.$x.':'.$fin.$x)->getStyle($deb.$x)->applyFromArray($style_entete_total);
		$col = 'B';
		$y = 2;
		$z=3;
		foreach($arrData ['instance'] as $value) {
			$sheet->setCellValue($col.$y, $value ['libelle'])->getColumnDimension($col)->setAutoSize(true);
			$sheet->getStyle($col.$y)->applyFromArray($style_instances);
			$col ++;
			$sheet->setCellValue($col.$y, '%')->getColumnDimension($col)->setAutoSize(true);
			$sheet->getStyle($col. $y)->applyFromArray($style_instances_taux);
			$col ++;
		}
		$sheet->setCellValue($col.$y, 'Total')->getColumnDimension($col)->setAutoSize(true);
		$sheet->getStyle($col.$y)->applyFromArray($style_instances);
		$col ++;
		$sheet->setCellValue($col.$y, '%')->getColumnDimension($col)->setAutoSize(true);
		$sheet->getStyle($col. $y)->applyFromArray($style_instances_taux);

		$col = 'A';
		$x = 3;
		$i = 1;
		foreach($statut as $key => $value) {
			if($i < 10) {
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				if($i>7)
					$sheet->getStyle($col.$x)->applyFromArray($style_solde);
					$col2 = 'B';
					$valTotal=0;
					$tauxTotal= 0;
					$totalGlobale=0;
					foreach($arrData ['instance'] as $value) {
						$taux=($value ['data']['total']!=0)?round(($value ['data'] [$key]/$value ['data'] ['total'])*100):0;
						$sheet->setCellValue($col2.$x, $value ['data'] [$key]);
						$sheet->getStyle($col2.$x)->applyFromArray($style_body_valeur);
						if($i>7)
							$sheet->getStyle($col2.$x)->applyFromArray($style_solde);
							$col2 ++;
							$sheet->setCellValue($col2.$x, $taux.'%');
							$sheet->getStyle($col2.$x)->applyFromArray($style_body_taux);
							if($i>7)
								$sheet->getStyle($col2.$x)->applyFromArray($style_solde);
								$col2 ++;
								$valTotal  = $valTotal + round($value ['data'] [$key]);
								$totalGlobale = $totalGlobale + round($value ['data'] ['total']);
					}
					$tauxTotal = ($totalGlobale!=0)?round(($valTotal/$totalGlobale)*100):0;
					$sheet->setCellValue($col2.$x, $valTotal);
					$sheet->getStyle($col2.$x)->applyFromArray($style_body_valeur);
					if($i>7)
						$sheet->getStyle($col2.$x)->applyFromArray($style_solde);
						$col2 ++;
						$sheet->setCellValue($col2.$x, $tauxTotal.'%');
						$sheet->getStyle($col2.$x)->applyFromArray($style_body_taux);
						if($i>7)
							$sheet->getStyle($col2.$x)->applyFromArray($style_solde);
			} elseif($i == 10) {
				/* Partie pour la ligne total soldé */
				$sheet->setCellValue($col.$x, 'Total Soldé')->getColumnDimension($col)->setAutoSize(true);
				$sheet->getStyle($col.$x)->applyFromArray($style_solde);
				$col2 = 'B';
				$totalSolde = 0;
				$tauxSolde = 0;
				$totalGlobale=0;
				foreach($arrData ['instance'] as $data) {
					$solde = round($data ['data'] ['nbSoldeeHorsDelais']  + $data ['data'] ['nbSoldeeDansLesDelais']);
					$taux  = ($data ['data']['total']!=0)?round(($solde/$data ['data']['total'])*100):0;
					$sheet->setCellValue($col2.$x, $solde);
					$sheet->getStyle($col2.$x)->applyFromArray($style_solde_valeur);
					$col2 ++;
					$sheet->setCellValue($col2.$x, $taux.'%');
					$sheet->getStyle($col2.$x)->applyFromArray($style_solde_taux);
					$col2 ++;
					$totalSolde = $totalSolde+$solde;
					$totalGlobale = $totalGlobale + round($data ['data'] ['total']);
				}
				$tauxTotal = ($totalGlobale!=0)?round(($totalSolde/$totalGlobale)*100):0;
				$sheet->setCellValue($col2.$x, $totalSolde);
				$sheet->getStyle($col2.$x)->applyFromArray($style_solde_valeur);
				$col2 ++;
				$sheet->setCellValue($col2.$x, $tauxTotal.'%');
				$sheet->getStyle($col2.$x)->applyFromArray($style_solde_taux);
				$x ++;
				/* Fin Partie pour la ligne total soldé */

				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$sheet->getStyle($col.$x)->applyFromArray($style_tot);
				$col2 = 'B';
				$valTotal=0;
				foreach($arrData ['instance'] as $value) {
					$sheet->setCellValue($col2.$x, $value ['data'] [$key]);
					$sheet->getStyle($col2.$x)->applyFromArray($style_tot);
					$col2 ++;
					$sheet->setCellValue($col2.$x, '100%');
					$sheet->getStyle($col2.$x)->applyFromArray($style_tot);
					$col2 ++;
					$valTotal = $valTotal+round($value ['data'] [$key]);
				}
				$sheet->setCellValue($col2.$x, $valTotal);
				$sheet->getStyle($col2.$x)->applyFromArray($style_tot);
				$col2 ++;
				$sheet->setCellValue($col2.$x, '100%');
				$sheet->getStyle($col2.$x)->applyFromArray($style_tot);
			} else {
				continue;
			}
			$x ++;
			$i ++;
		}
		return $sheet;
	}

	/**
	 * @param array $arrData
	 * @param array $data
	 * @return array
	 */
	private function addPercent($arrData, $data, $denom) {
		foreach($data as $key => $value) {
			if($arrData[$denom]) {
				$taux = $arrData[$value] * 100 / $arrData[$denom];
				$arrData[$key] = round($taux).'%';
			} else {
				$arrData[$key] = null;
			}
		}
		return $arrData;
	}
}