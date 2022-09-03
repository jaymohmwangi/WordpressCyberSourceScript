<?php
    Class db{
        
     protected function connect(){

        $servername = "167.99.248.146";
        $username = "wigo_wallet";
        $password = '[aFM$D](tDt{';
        $dbname = "cybersource";

        try {
            $conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully"; 
            return $conn;
            }
            
        catch(PDOException $e)
            {
            echo "Connection failed: " . $e->getMessage();
            }
     }

    }
?>
