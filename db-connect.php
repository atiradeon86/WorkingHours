<?php
$serverName = "";
  /*  $connectionInfo = array( 
						"Database"=>"bryanDB",
						"ReturnDatesAsStrings"=>true,
                        "UID"=>"",
                        "PWD"=>"",
                        "Encrypt"=>false,
                        "TrustServerCertificate"=>true);
*/
                        $connectionInfo = array( 
                            "Database"=>"",
                            "UID"=>"",
                            "PWD"=>"",
                            "ReturnDatesAsStrings"=>false,
                            "Encrypt"=>false,
                            "TrustServerCertificate"=>true);
						
    $conn = sqlsrv_connect( $serverName, $connectionInfo);
    
    if( $conn === false ) {
        die( print_r( sqlsrv_errors(), true));
   }   
?>
