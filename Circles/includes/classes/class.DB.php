    <?php

    class DB {
    	private static $_instance = null; 
    	       public 	$_pdo,  
                        $_errorDesc,
    	               	$_query, 
    		       		$_error = false, 
    		       		$_results, 
                        $_lastID,
    		       		$_count = 0; 
     
            private function __construct(){
                    try{
                            $this->_pdo = new PDO('mysql:host=' .DATABASE_HOST. ';dbname=' .DATABASE_NAME, DATABASE_USERNAME,DATABASE_PASSWORD);

                        
                               
                    }catch(PDOException $e){
                            die($e->getMessage());
                    }

            }
     
            public static function getInstance() {
                    if(!isset(self::$_instance)) {
                            self::$_instance = new DB();
                    }
                    return self::$_instance;
            }

                /**
                *The Main function of our DB wrapper which will be used to execute queries can also be used directly from within the document
                * @param sql - the query
                * @param params - It is basically to replace ? with the values that are  provided in the given array  . This is done to prevent sql injection e.g the array provided is ("aliaa08","absm@i2s.co.in") and we have the following SQL  "SELECT * FROM user2 WHERE name =? AND email=? so this will basically replace the ? s with array values after sanatizing it"
                * @param class - initially set to null is an advanced option which will be used to create personalized result e.g we may wanna calculate bday of the user ,so what we will then do is craete a class which on basis of existing data will callibarate the bday and will create a new field as bday or whatever . So easy!!
                */
                public function query($sql, $params = array(),$class=null) {
                    $this->_error = false;
                    if($this->_query = $this->_pdo->prepare($sql)) {
                         

                                if (count($params))
                                {

                                    $pos=1;
                                    foreach ($params as $param) {
                                        $this->_query->bindValue($pos,$param);
                                          
                          
                                        $pos++;
                                    }

                                }
                                if ($this->_query->execute())
                                {

                                  
                                    if ($class!="")
                                    {
                                        $this->_query->setFetchMode(PDO::FETCH_CLASS,$class);
                                        $this->_results=$this->_query->fetchAll();

                                       
                                    }

                                    else
                                    {   


                                   $this->_results =$this->_query->fetchAll(PDO::FETCH_OBJ);

                                    }
                                      $this->_lastID= $this->_pdo->lastInsertId();
                                   
                                     $this->_count =$this->_query->rowCount();
                                    
                                }
                                else
                                {
                                   $this->_error= true;
                                   $this->_errorDesc=$this->_query->errorInfo();
                                }

                            }
                            return $this;
                        }

                    /**
                    * The middle interface between the get,delete, update , insert and query function
                    * @param action - such as SELECT , DELETE , UPDATE etc
                    * @param table  - name of the table
                    * @param where  - array of field operator value e.g username = aliaa08
                    */
                    public function action($action, $table, $where = array()) {
                    if(count($where) === 3){
                            $operators = array('=', '>', '<', '>=', '<=');
     
                            $field          = $where[0];
                            $operator       = $where[1];
                            $value          =$where[2];//Sql Injection , i know we already have prepared statements ..but still.Thats the whole point of PDO LOL!!
     
                            if(in_array($operator, $operators)) {
                                    $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                                    if(!$this->query($sql, array($value))->error()) {



                                            return $this;

                                    }
                            }
                    }
                    return false;

                     }
     
            /**
            * Get main function for fetching results
            * @param $table - Name of the table 
            * @param $where- array of field ,operator, value
            * @param $fields - If ony specific columns have to be fetched it also is an array
            */
            public function get($table, $where,$fields=array()){


                    if (count($fields))

                        
                        {

                                $field_sql="`".implode($fields,"`,`")."`";
                                
                        }
                        else
                        {

                            $field_sql="*";
                        }
                      
                      return $this->action("SELECT $field_sql ", $table, $where);

            }
     
              /**
            * Delete main function for deleting results
            * @param $table - Name of the table 
            * @param $where - array of field ,operator, value
             */
            public function delete($table, $where) {
                    return $this->action('DELETE', $table, $where);

            }

            /**
            * The Main insert function for any insert operation on Konnect
            * @param table  - the name of table where something has to be inserted
            * @param fields - array of the values such as ('name'=>'name value','email'=>'something@i2s.co.in').Where the key is the column name and its value is the value to be inserted
            */
            public function insert($table,$fields=array())
            {

                if (count($fields))
                {

                    $keys=array_keys($fields);
                    $values=[];
                    foreach ($fields as $key => $value) {
                        
                                $value=htmlentities($value);// Foil XSS attacks
                                array_push($values,$value);
                    }
                    $values=implode("','",$values);
                     
                          $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ('{$values}')";
                            
                          if (!$this->query($sql,$fields)->error())
                          {
                          return true;          
                             }
                            
                         }
                         return false;
                
            }
            /**
            * The Main Update function to be used on konnect for all update operations 
            * @param table  - name of the table to be updated
            * @param params - array of the column operator and value eg username = aliaa08
            * @param fields - a simple array of a key and a value where the key is the collumn to be updated and the value is the value to be updated
            */ 
            public function update($table,$params,$fields)
            {


                $set='';
                $x=1;

                $column=$params[0];
                $operator=$params[1];
                $id=$params[2];
                foreach ($fields as $name => $value) {
                   
                     $set .="{$name}=?";
                    if ($x<count($fields))
                    {

                        $set .=',';
                    }
                    $x++;
                   
                }
                $sql="UPDATE `{$table}` SET {$set} WHERE `{$column}` {$operator} '{$id}' ";
                   
              
                 if (!$this->query($sql,$fields)->error())
                          {
                          return true;          
                             }
                
            }
            /**
            * Some kind of an accesor function ,it just returns the fetched value as an ASSOC array after the query has been successfully executed
            */
            public function results()
            {

                return $this->_results;
            }
            /**
            * Some kind of an accesor function ,it just returns the error case as 0 for no error and 1 for some error
            */
            public function error()
             {

              return $this->_error;

             }
             /**
            * Some kind of an accesor function ,it just returns the number of rows returned after successful execution of a query
            */
            public function rowCount()
            {

                 return $this->_count;
             }
            /**
            * It just returns the first value fetched
            */
            public function first()
            {
                               
              return $this->results()[0]; //Error here

            }
            /**
            * Some kind of an accesor function ,it returns the error as an array with complete description etc
            */

              public function getError()
             {

               return $this->_errorDesc;
                             
            }     
             /**
            * Some kind of an accesor function ,it returns the id of the last inserted query
            */
            public function lastId()
            {

                return $this->_lastID;
                 
            }

            public function showQuery()
            {

                return  $this->_query;
            }
        } 
