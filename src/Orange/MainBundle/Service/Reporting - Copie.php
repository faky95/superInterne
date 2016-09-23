<?php
namespace Orange\MainBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\Serializer\Encoder\ChainEncoder;

class Reporting extends \PHPExcel {
	public function reportingInstanceAction($arrData, $statut){
		$this->removeSheetByIndex(0);
		$this->createSheet(0);
		$this->setActiveSheetIndex(0);
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
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
	
	public function reportingStructureAction($arrData, $statut){
		$this->removeSheetByIndex(0);
		$this->createSheet(0);
		$this->setActiveSheetIndex(0);
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
		
		$objWriter = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		return $objWriter;
	}
}