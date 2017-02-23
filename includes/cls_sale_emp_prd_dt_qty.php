<?php

class cls_sale_prd_dt_qty
{
	var $sPrdName;
	var $sDate;
	var $iQty;

	function cls_sale_prd_dt_qty($sPrdName, $sDate, $iQty){
		$this->sPrdName = $sPrdName;
		$this->sDate = $sDate;
		$this->iQty = $iQty;
	}
}


class cls_sale_emp_prd_dt_qty
{
	var $sEmpName;
	var $aSaleData = Array();
	var $aPrdSold = Array();
	var $iSaleRec=0;
	var $aEmpSaleMatrix = Array();

	function cls_sale_emp_prd_dt_qty($sEmpName){
		$this->sEmpName = $sEmpName;
		//$this->aSaleData[$iSaleRec] = new cls_sale_prd_dt_qty($sPrdName, $sDate, $iQty);
		$iSaleRec = 0;
	}
	function addSaleData($sPrdName, $sDate, $iQty){
		//$this->aSaleData[$this->iSaleRec] = new cls_sale_prd_dt_qty($sPrdName, $sDate, $iQty);
		//$this->aSaleData[$this->iSaleRec][0]=$sPrdName;
		//$this->aSaleData[$this->iSaleRec][1]=$sDate;
		//$this->aSaleData[$this->iSaleRec][2]=$iQty;
		//$this->iSaleRec += 1;
		
		$this->aSaleData[$this->iSaleRec][0]=$sPrdName;
		$this->aSaleData[$this->iSaleRec][1]=$sDate;
		$this->aSaleData[$this->iSaleRec][2]=$iQty;
		$this->iSaleRec += 1;

		
	}
	function printSaleData()
	{
	
		echo "<h2>".$this->sEmpName."</h2><br><table border=1>";
	
		for($a=0; $a<$this->iSaleRec; $a++)
		{
			echo "<tr>";
			echo "<td>". $this->aSaleData[$a][0];
			echo "<td>". $this->aSaleData[$a][1];
			echo "<td>". $this->aSaleData[$a][2];
			echo "</tr>";
		}
		echo "</table>";
	}
	function addTotals2Matrix()
	{
		//Get Row & Column count
		$iProductsCount = count($this->aPrdSold);
		$iRowCount = count($this->aEmpSaleMatrix);

		//Loop through to add the numbers
		for($a=0; $a<$iRowCount; $a++)
		{

			for($b=1; $b<$iProductsCount; $b++) //Start from 1 (First Column is Date)
			{
					$iQty = is_null($this->aEmpSaleMatrix[$a][$b])?0:$this->aEmpSaleMatrix[$a][$b];
					
					$this->aEmpSaleMatrix[$a][$iProductsCount] += $iQty; //row total
					$this->aEmpSaleMatrix[$iRowCount][$b] += $iQty; //col total
					$this->aEmpSaleMatrix[$iRowCount][$iProductsCount] += $iQty; //Grand total
					//echo "<br>[".$a.",".$b."]=".$iQty;
			}
		}
		//Set the caption for Total
		$this->aPrdSold[$iProductsCount] = "Total";
		$this->aEmpSaleMatrix[$iRowCount][0] = "Total";
		
		$iProductsCount = count($this->aPrdSold);
		$iRowCount = count($this->aEmpSaleMatrix);
		//echo " Updated to (Rows=".$iRowCount.";Cols=".$iProductsCount.")";
	}
	function buildPrdDateMatrix()
	{
		//$aEmpSaleMatrix = Array();
		//$iPrdRow = 0;
		$iPrdCol = 0;
		$iDtRow = -1;
		$sPrdName_old = "";
		$sDate_old = "";
		//$bFirst = true;

		$this->aPrdSold[$iPrdCol]="*";
		
		//Add data
		$rowcount = count($this->aSaleData);
		for($r=0; $r < $rowcount; $r++)
		{
		
			$sPrdName = $this->aSaleData[$r][0]; //Prod name
			$sDate = $this->aSaleData[$r][1]; //Sale dt
			$iQty = $this->aSaleData[$r][2]; //Qty
		
			//Add Product to array
			if(array_search($sPrdName, $this->aPrdSold)){
				$iPrdCol = array_search($sPrdName, $this->aPrdSold);
			}else{
				$iPrdCol += 1;
				$this->aPrdSold[$iPrdCol]=$sPrdName; //Col header
				//sort($this->aPrdSold);
			}
			//Add Date to array
			if($sDate != $sDate_old )
			{
				$iDtRow += 1;
				$this->aEmpSaleMatrix[$iDtRow][0]=$sDate; //Row Header
			}
			//echo "<br>Line#=".$r."->".$sPrdName."/".$sDate."/".$iQty.";@ iDtRow=".$iDtRow."; iPrdCol=".$iPrdCol;
			
			$this->aEmpSaleMatrix[$iDtRow][$iPrdCol]=$iQty; //Cell data
				
			$sPrdName_old = $sPrdName;
			$sDate_old = $sDate;
		
		}

		$this->addTotals2Matrix();
		//Merge both to make single
		$this->aEmpSaleMatrix = $this->mergeHeaderAndDataMtrx($this->aPrdSold, $this->aEmpSaleMatrix);
		//sort($this->aEmpSaleMatrix);
	}
	
	function printSaleMatrix()
	{

		$iProductsCount = count($this->aPrdSold);
		$iRowCount = count($this->aEmpSaleMatrix);
		//echo "Printing emp:".$this->sEmpName." (Rows=".$iRowCount.";Cols=".$iProductsCount.")";
		
		//Print products /column
 		echo "<h2>".$this->sEmpName."</h2><br><table class='repnw' border=1>"; //<tr class='rptgrp1h'>";

		//Print matrix
 		for($a=0; $a<$iRowCount; $a++)
 		{
 			echo "<tr>";
 			for($b=0; $b<$iProductsCount; $b++)
 				if($a==0 || $b==0)
 					echo "<th width=5 class='mtrxh'>". $this->aEmpSaleMatrix[$a][$b]."</th>";
 				elseif($a==$iRowCount-1 || $b==$iProductsCount-1)
 					echo "<th class='mtrxh_num'>". $this->aEmpSaleMatrix[$a][$b]."</th>";
 				else
 					echo "<td class='mtrxd_num'>". $this->aEmpSaleMatrix[$a][$b]."</td>";
 			echo "</tr>";
 		}
 		echo "</table>";
	}
	
	function mergeHeaderAndDataMtrx($aPrdSold, $aEmpSaleMatrix)
	{
		$aTempMtrx = Array();
		$iProductsCount = count($aPrdSold);
		$iRowCount = count($aEmpSaleMatrix);
		
		//echo "Header: ";
		for($a=0; $a<$iProductsCount; $a++)
		{
			$aTempMtrx[0][$a]=$aPrdSold[$a];
			//echo " # ".$aPrdSold[$a];
		}
		//echo "<br>Data:";
		//Print matrix
		for($a=0; $a<$iRowCount; $a++)
		{
			for($b=0; $b<$iProductsCount; $b++){
				$aTempMtrx[$a+1][$b]=$aEmpSaleMatrix[$a][$b];
				//echo " # ".$aEmpSaleMatrix[$a][$b];
			}
		}
		return $aTempMtrx;
		
	}
	function buildPrdDateMatrix2()
	{
		//This is for the Object matrix ***NOT WORKING YET***
		/*
		$table = array();
		$prd_names = array();
		$aDtTotal = array();
		
		foreach ($this->aSaleData as $score)
		{
			$prd_names[] = $score->sPrdName;
			$table[$score->sDate][$score->sPrdName] = $score->iQty;
			$total[$score->sPrdName] += $score->iQty;
		}
		
		$prd_names = array_unique($prd_names);
		return $table;*/
	}
}





?>