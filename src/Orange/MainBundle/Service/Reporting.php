<?php
namespace Orange\MainBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\Serializer\Encoder\ChainEncoder;

class Reporting extends \PHPExcel {
	public function reportingInstanceAction($arrData, $statut,$actions, $statuts){
		$objPHPExcel = new \PHPExcel();
		
		$this->removeSheetByIndex(0);
		$this->createSheet(0);
		$this->setActiveSheetIndex(0);
		if($this->getSheetCount()>1){
			for ($i=1;$i<$this->getSheetCount();$i++)
				$this->removeSheetByIndex($i);
		}
		
		$sheet2 = $objPHPExcel->createSheet(1);
		$sheet3 = $objPHPExcel->createSheet(2);
		
		$a = Reporting::exportAction($sheet2, $actions, $statuts);
		$x=1;
		$col = 'B';
		$style_instance = array(
				'alignment' => array(
					'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,	
				),
				'fill' => array(
					'type' => \PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'ffc000'),
				),
				'font' => array(
						'bold' => true,
						'size' => 18,
						'color' => array('rgb' => '000000')
				));
		$style_stat = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => true,
						'size' => 14,
						'color' => array('rgb' => '000000')
				));
		$style_inst = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => true,
						'size' => 12,
						'color' => array('rgb' => 'ffc000')
				));
		$style_valeur = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => false	,
						'size' => 10,
						'color' => array('rgb' => '000000')
				));
		$style_valeur_total = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => true	,
						'size' => 14,
						'color' => array('rgb' => '000000')
				));
		$style_libelle_total = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				),
				'font' => array(
						'bold' => true	,
						'size' => 14,
						'color' => array('rgb' => '000000')
				));
		$style_taux = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => true,
						'size' => 14,
						'color' => array('rgb' => 'ff6600')
				));
		$style_libelle_taux = array(
				'font' => array(
						'bold' => true,
						'size' => 14,
						'color' => array('rgb' => 'ff6600')
				));
		$this->getActiveSheet()->setCellValue('A2', "Statut")->getColumnDimension('A')->setAutoSize(true);
		$this->getActiveSheet()->getStyle('A2')->applyFromArray($style_stat);
		$size = sizeof($arrData['instance']);
		$n = \PHPExcel_Cell::columnIndexFromString($col);
		$finCol = \PHPExcel_Cell::stringFromColumnIndex(($n-2)+$size);
		$this->getActiveSheet()->setCellValue($col.$x, "Instances")->getColumnDimension($col)->setAutoSize(true);
		$this->getActiveSheet()->mergeCells($col.$x.':'.$finCol.$x)->getStyle($col.$x)->applyFromArray($style_instance);
		$col = 'B';
		$y=2;
		foreach ($arrData['instance'] as $value){
			$this->getActiveSheet()->setCellValue($col.$y, $value['libelle'])->getColumnDimension($col)->setAutoSize(true);
			$this->getActiveSheet()->getStyle($col.$y)->applyFromArray($style_inst);
			$col++;
		}
		$col = 'A';
		$x = 3;
		$i=1;
		foreach ($statut as $key => $value){
			if ($i < 9){
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$col2 = 'B';
				foreach ($arrData['instance'] as $value){
					$this->getActiveSheet()->setCellValue($col2.$x, $value['data'][$key]);
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_valeur);
					$col2++;
				}
			}elseif ($i == 9){
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_libelle_total);
				$col2 = 'B';
				foreach ($arrData['instance'] as $value){
					$this->getActiveSheet()->setCellValue($col2.$x, $value['data'][$key]);
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_valeur_total);
					$col2++;
				}
			}
			else{
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_libelle_taux);
				$col2 = 'B';
				foreach ($arrData['instance'] as $value){
					$this->getActiveSheet()->setCellValue($col2.$x, $value['data'][$key].'%');
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_taux);
					$col2++;
				}
			}
			
			$x++;
			$i++;
		}

		$this->addSheet($a);
		$this->getActiveSheet()->setTitle('stats_par_instance');
		$sheet2->setTitle('actions');
		$sheet3->setTitle('Synthèse par instance');
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
	public function reportingStructureAction($arrData, $statut,$actions, $statuts){
		
		$objPHPExcel = new \PHPExcel();
		
		$this->removeSheetByIndex(0);
		$this->createSheet(0);
		$this->setActiveSheetIndex(0);

		$sheet2 = $objPHPExcel->createSheet(1);
		$a = Reporting::exportAction($sheet2, $actions, $statuts);
		$x=1;
		$col = 'B';
		$style_instance = array(
				'alignment' => array(
					'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,	
				),
				'fill' => array(
					'type' => \PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'ffc000'),
				),
				'font' => array(
						'bold' => true,
						'size' => 18,
						'color' => array('rgb' => '000000')
				));
		$style_stat = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => true,
						'size' => 14,
						'color' => array('rgb' => '000000')
				));
		$style_inst = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => true,
						'size' => 12,
						'color' => array('rgb' => 'ffc000')
				));
		$style_valeur = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => true,
						'size' => 12,
						'color' => array('rgb' => '000000')
				));
		$style_taux = array(
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'font' => array(
						'bold' => true,
						'size' => 14,
						'color' => array('rgb' => 'ff6600')
				));
		$style_libelle_taux = array(
				'font' => array(
						'bold' => true,
						'size' => 14,
						'color' => array('rgb' => 'ff6600')
				));
		$this->getActiveSheet()->setCellValue('A2', "Statut")->getColumnDimension('A')->setAutoSize(true);
		$this->getActiveSheet()->getStyle('A2')->applyFromArray($style_stat);
		$size = sizeof($arrData['structure']);
		$n = \PHPExcel_Cell::columnIndexFromString($col);
		$finCol = \PHPExcel_Cell::stringFromColumnIndex(($n-2)+$size);
		$this->getActiveSheet()->setCellValue($col.$x, "Structures")->getColumnDimension($col)->setAutoSize(true);
		$this->getActiveSheet()->mergeCells($col.$x.':'.$finCol.$x)->getStyle($col.$x)->applyFromArray($style_instance);
		$col = 'B';
		$y=2;
		foreach ($arrData['structure'] as $value){
			$this->getActiveSheet()->setCellValue($col.$y, $value['libelle'])->getColumnDimension($col)->setAutoSize(true);
			$this->getActiveSheet()->getStyle($col.$y)->applyFromArray($style_inst);
			$col++;
		}
		$col = 'A';
		$x = 3;
		$i=1;
		foreach ($statut as $key => $value){
			if ($i <= 9){
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$col2 = 'B';
				foreach ($arrData['structure'] as $value){
					$this->getActiveSheet()->setCellValue($col2.$x, $value['data'][$key]);
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_valeur);
					$col2++;
				}
			}
			else{
				$this->getActiveSheet()->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$this->getActiveSheet()->getStyle($col.$x)->applyFromArray($style_libelle_taux);
				$col2 = 'B';
				foreach ($arrData['structure'] as $value){
					$this->getActiveSheet()->setCellValue($col2.$x, $value['data'][$key].'%');
					$this->getActiveSheet()->getStyle($col2.$x)->applyFromArray($style_taux);
					$col2++;
				}
			}
			
			$x++;
			$i++;
		}
		
		// 		exit('dd');
		$this->addSheet($a);
		$this->getActiveSheet()->setTitle('stats_par_structure');
		$sheet2->setTitle('actions');
		
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
public function exportAction($sheet, $arrData, $dataStatut){
	
		$arrayStatut = array();
		foreach ($dataStatut as $statut){
			$arrayStatut[$statut->getCode()] = $statut->getLibelle();
		}
		$default_border = array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'size' => 16, 'color' => array('rgb'=>'000000'));
		$style_th = array(
				'borders' => array('top' => $default_border
						,'bottom' => $default_border
						,'left' => $default_border
						,'right' => $default_border),
				'alignment' => array(
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb'=>'ff6600'),
				),
				'font' => array(
						'bold' => true,
						'size' => 16,
						'color' => array('rgb' => '000000')
				));
		$data = array(
				'borders' => array('top' => $default_border
						,'bottom' => $default_border
						,'left' => $default_border
						,'right' => $default_border),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'fill' => array(
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => array('rgb'=>'ffffff'),
				),
				'font' => array(
						'size' => 13,
						'color' => array('rgb' => '000000')
				));
		$action = array(
				'borders' => array('top' => $default_border
						,'bottom' => $default_border
						,'left' => $default_border
						,'right' => $default_border),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				));
		$desc = array(
				'borders' => array('top' => $default_border
						,'bottom' => $default_border
						,'left' => $default_border
						,'right' => $default_border),
				'alignment' => array(
						'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				));
		$objPHPExcel = new \PHPExcel();
		$th = array('Référence', 'Instance', 'Libellé', 'Description', 'Priorité', 'Porteur', 'Direction', 'Pôle', 'Département', 'Service', 'Type',
				'Statut', 'Domaine' ,'Contributeurs', 'Date de début' ,'Date de fin prévue' ,'Date de clôture','Avancements'
		);
		$col = "A";
		$x=1;
		foreach ($th as $value){
			if($col == "C"){
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$sheet->getStyle($col.$x)->applyFromArray($style_th);
				$col++;
			}
			elseif($col == "D"){
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$sheet->getStyle($col.$x)->applyFromArray($style_th);
				$col++;
			}elseif($col == "R"){
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setWidth('50');
				$sheet->getStyle($col.$x)->applyFromArray($style_th);
				$col++;
			}else{
				$sheet->setCellValue($col.$x, $value)->getColumnDimension($col)->setAutoSize(true);
				$sheet->getStyle($col.$x)->applyFromArray($style_th);
				$col++;
			}
		}
		$y=2;
		$b = "A";
		if($arrData){
			foreach ($arrData as $value){
				$b = "A";
				if($b == "A"){
					$sheet->setCellValue($b.$y, $value->getReference())->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "B"){
					$sheet->setCellValue($b.$y, $value->getInstance()? $value->getInstance()->__toString():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "C"){
					$sheet->setCellValue($b.$y, $value->getLibelle());
					// 				$sheet->getStyle($b.$y)->applyFromArray($action)->getAlignment()->setWrapText(true);
					$b++;
				}if($b == "D"){
					$sheet->setCellValue($b.$y, $value->getDescription());
					// 				$sheet->getStyle($b.$y)->applyFromArray($desc)->getAlignment()->setWrapText(true);
					$b++;
				}if($b == "E"){
					$sheet->setCellValue($b.$y, $value->getPriorite()?$value->getPriorite()->__toString():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "F"){
					$sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getCompletNom():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "G"){
					//$sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getDirection():' ')->getColumnDimension($b)->setAutoSize(true);
					$sheet->setCellValue($b.$y, $value->getStructure()?$value->getStructure()->getArchitectureStructure()->getDirection():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "H"){
					// 				$sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getPole():' ')->getColumnDimension($b)->setAutoSize(true);
					$sheet->setCellValue($b.$y, $value->getStructure()?$value->getStructure()->getArchitectureStructure()->getPole():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "I"){
					// 				$sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getDepartement():' ')->getColumnDimension($b)->setAutoSize(true);
					$sheet->setCellValue($b.$y, $value->getStructure()?$value->getStructure()->getArchitectureStructure()->getDepartement():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "J"){
					//$sheet->setCellValue($b.$y, $value->getPorteur()?$value->getPorteur()->getService():' ')->getColumnDimension($b)->setAutoSize(true);
					$sheet->setCellValue($b.$y, $value->getStructure()?$value->getStructure()->getArchitectureStructure()->getService():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "K"){
					$sheet->setCellValue($b.$y, $value->getTypeAction()?$value->getTypeAction()->__toString():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "L"){
					$sheet->setCellValue($b.$y, $arrayStatut[$value->getEtatReel()])->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "M"){
					$sheet->setCellValue($b.$y, $value->getDomaine()?$value->getDomaine()->__toString():' ')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "N"){
					$sheet->setCellValue($b.$y, $value->getAllContributeur())->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "O"){
					$sheet->setCellValue($b.$y, $value->getDateDebut()->format('d-m-Y'))->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "P"){
					$sheet->setCellValue($b.$y, $value->getDateInitial()->format('d-m-Y'))->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "Q"){
					$sheet->setCellValue($b.$y, $value->getDateFinExecut() ? $value->getDateFinExecut()->format('d-m-Y'):'En Cours')->getColumnDimension($b)->setAutoSize(true);
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}if($b == "R"){
					$sheet->setCellValue($b.$y, $value->getAllAvancement());
					// 				$sheet->getStyle($b.$y)->applyFromArray($data);
					$b++;
				}
				$y++;
			}
		}
		$objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);
		return $sheet;
	}
	
	public function syntheseInstance(){
		
	}
	
}