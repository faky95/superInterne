<?php
namespace Orange\QuickMakingBundle\Service;

class PHPExcelAdvanced extends \PHPExcel {
	
	/**
	 * @param string $author
	 * @param string $title
	 * @param string $subject
	 * @param string $description
	 * @param string $keywords
	 * @param string $category
	 */
	public function setDocumentProperties($author, $title, $subject, $description, $keywords, $category) {
		// Set some meta data relative to the document
		$this->getProperties()->setCreator($author);
		$this->getProperties()->setLastModifiedBy($author);
		$this->getProperties()->setTitle($title);
		$this->getProperties()->setSubject($subject);
		$this->getProperties()->setDescription($description);
		$this->getProperties()->setKeywords($keywords);
		$this->getProperties()->setCategory($category);
	}
	
	/**
	 * @param number $row
	 * @param string $firstColumn
	 * @param array $fields
	 * @param number $width
	 * @param number $size
	 */
	public function placeColumns($row, $firstColumn, $fields, $width = 30, $size = 14) {
		// NOTE: $column = 'A'; $column + 1 == 1; $column++ == 'B'; True story.
		// Get the final column index and create the excel column to table field map
		$fieldsCount = count($fields);
		$nColumns = $firstColumn;
		$excelMap = array();
		for($i = 0; $i < $fieldsCount; $i ++) {
			$excelMap[$nColumns] = $fields[$i];
			$nColumns ++;
		}
		// Set the first row as the table's field names
		for($j = $firstColumn; $j < $nColumns; $j ++) {
			$this->getActiveSheet()->setCellValue($j.$row, $excelMap[$j]);
			$this->getActiveSheet()->getColumnDimension($j)->setWidth($width);
			// Donner un style à la feuille courante
			$this->getActiveSheet()->duplicateStyleArray(array(
					'font' => array('bold' => true, 'size' => $size, 'name' => 'Arial'),
					'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER),
					'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN)) 
				), $j.$row);
		}
	}
	
	/**
	 * @param string $value
	 * @param string $column
	 * @param array $styleArray
	 */
	public function setRichText($value, $column, $styleArray = array()) {
		$this->getActiveSheet()->setCellValue($column, $value);
		$font = $this->getActiveSheet()->getStyle($column)->getFont();
		if(isset($styleArray['bold'])) {
			$font->setBold(true);
		}
		if(isset($styleArray['alignX'])) {
			$this->getActiveSheet()->getStyle($column)->getAlignment()->setHorizontal($styleArray['alignX']);
		}
		if(isset($styleArray['alignY'])) {
			$this->getActiveSheet()->getStyle($column)->getAlignment()->setVertical($styleArray['alignY']);
		}
		if(isset($styleArray['color'])) {
			$font->setColor(new \PHPExcel_Style_Color($styleArray['color']));
		}
		if(isset($styleArray['wrap'])) {
			$this->getActiveSheet()->getStyle($column)->getAlignment()->setWrapText(true);
		}
		if(isset($styleArray['size'])) {
			$font->setSize($styleArray['size']);
		}
		if(isset($styleArray['police'])) {
			$font->setName($styleArray['police']);
		}
		if(isset($styleArray['border'])) {
			$this->getActiveSheet()->getStyle($column)->applyFromArray(array(
					'borders' => array('allborders' => array('style' => $styleArray['border']))
				));
		}
		if(isset($styleArray['format'])) {
			switch($styleArray['format']) {
				case 2 :
					$format = '#,##0.00';
					break;
				case 1 :
					$format = '#,##0.0';
					break;
				case - 1 :
					$format = '#,##0';
					break;
				default :
					$format = '#,###';
					break;
			}
			$this->getActiveSheet()->getStyle($column)->getNumberFormat()->setFormatCode($format);
		}
	}
	
	/**
	 * @param array $data
	 * @param array $excelMap
	 * @param number $row
	 */
	public function addRowData($data, $excelMap, $row) {
		foreach($excelMap as $column => $field) {
			$this->getActiveSheet()->setCellValue($column.$row, $data[$field]);
		}
	}
	
	/**
	 * @param array $data
	 * @param number $row
	 * @param array $styleArray
	 */
	public function addRowLabel($data, $row, $styleArray = array()) {
		foreach($data as $column => $value) {
			$this->setRichText($value, $column.$row, $styleArray);
		}
	}
	
	/**
	 * @param string $image
	 * @param string $coordinate
	 * @param number $size
	 * @param number $position
	 * @return \PHPExcel_Worksheet_Drawing
	 */
	public function addImage($image, $coordinate, $size, $position) {
		$objDrawing = new \PHPExcel_Worksheet_Drawing();
		$objDrawing->setPath($image);
		if(isset($size[0]) && $size[0]) {
			$objDrawing->setWidth($size[0]);
		}
		if(isset($size[1]) && $size[1]) {
			$objDrawing->setWidth($size[1]);
		}
		$objDrawing->setCoordinates($coordinate);
		if(isset($position[0]) && $position[0]) {
			$objDrawing->setOffsetX($position[0]);
		}
		if(isset($position[1]) && $position[1]) {
			$objDrawing->setOffsetY($position[1]);
		}
		$objDrawing->setWorksheet($this->getActiveSheet());
		return $objDrawing;
	}
	
	/**
	 * @param array $cells
	 */
	public function mergeCells($cells) {
		foreach($cells as $cell) {
			$this->getActiveSheet()->mergeCells($cell[0].':'.$cell[1]);
		}
	}
	
	/**
	 * @param array $columns
	 */
	public function setColumnWidth($columns) {
		foreach($columns as $column => $width) {
			$this->getActiveSheet()->getColumnDimension($column)->setWidth($width);
		}
	}
	
	/**
	 * @param array $rows
	 */
	public function setRowHeight($rows) {
		foreach($rows as $row => $height) {
			$this->getActiveSheet()->getRowDimension($row)->setRowHeight($height);
		}
	}
	
	/**
	 * @param array $ranges
	 * @param array $styleArray
	 */
	public function applyStyleRanges($ranges, $styleArray = array()) {
		foreach($ranges as $range) {
			if(isset($styleArray['outline_border'])) {
				$this->getActiveSheet()->getStyle($range)->applyFromArray(array(
						'borders' => array('outline' => $styleArray['outline_border']) 
					));
			}
			if(isset($styleArray['inside_border'])) {
				$this->getActiveSheet()->duplicateStyleArray(array(
						'borders' => array('inside' => $styleArray['inside_border']) 
				), $range);
			}
			if(isset($styleArray['bgcolor'])) {
				$this->getActiveSheet()->duplicateStyleArray(array(
						'fill' => array (
							'type' => \PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => $styleArray['bgcolor']) 
						) 
				), $range);
			}
		}
	}
	
	/**
	 * @param string $type
	 * @return string
	 */
	public function closeReport($type) {
		$writer = \PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		$dateCreation = date('Ymdhis');
		$filePath = realpath("./uploads/$type").DIRECTORY_SEPARATOR."report-$dateCreation.xlsx";
		$writer->save($filePath);
		return "/uploads/$type/report-$dateCreation.xlsx";
	}
}