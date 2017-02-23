<?php

    //if(isset($_POST['submit']))
    //{
    //Import the PhpJasperLibrary
    include_once("PhpJasperLibrary/tcpdf/tcpdf.php");
    include_once("PhpJasperLibrary/PHPJasperXML.inc.php");
    //database connection details

    $server="localhost";
    $db="mycompany";
    $user="km";
    $pass="Asdf@1234";
    $version="0.8b";
    //$pgport=5432;
    //$pchartfolder="./class/pchart2";


    //display errors should be off in the php.ini file
    ini_set('display_errors', 0);

    //setting the path to the created jrxml file
    $xml =  simplexml_load_file("reports/rpt_emp_list.jrxml");

    $PHPJasperXML = new PHPJasperXML();
    //$PHPJasperXML->debugsql=true;
    //$PHPJasperXML->arrayParameter=array("acc_id"=>$P{parameter1})‌​);
    $PHPJasperXML->xml_dismantle($xml);

    $PHPJasperXML->transferDBtoArray($server,$user,$pass,$db);
    $PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file

//}
?>