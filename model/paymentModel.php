<?php
    include_once "config/db.php";
    class paymentModel extends db{
        //insert function
        public function insert($fields){
            $impClm = implode(', ', array_keys($fields));
            $impHolder = implode(', :', array_keys($fields));

            $sql = "INSERT INTO payments ($impClm) VALUES (:".$impHolder.")";
            $state = $this->connect()->prepare($sql);
            foreach($fields as $key => $value){
                $state->bindValue(':'.$key,$value);
            }
            $stateExec = $state->execute();

            if($stateExec){
                return true;
            }
        }

        //getting one item function
        public function selectOne($id){
            $sql = "SELECT * FROM payments WHERE invoice_id=:id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindValue(":id",$id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        //update function
        public function update($fields,$id){
           $st = "";
           $counter = 1;
           $total_fields = count($fields);

           foreach($fields as $key=>$value){
               if($counter === $total_fields){
                   $set = "$key = :".$key;
                   $st = $st.$set;
               }else{
                $set = "$key = :".$key.", ";
                $st = $st.$set; 
                $counter++;
               }
           }

           $sql = "";
           $sql.= "UPDATE payments SET ".$st;
           $sql.= " WHERE id =".$id;

           $stmt = $this->connect()->prepare($sql);

           foreach($fields as $key => $value){
               $stmt->bindValue(':'.$key, $value);
           }

           $stmtExec = $stmt->execute();

           if($stmtExec){
            return true;
           }
        }



    }
?>