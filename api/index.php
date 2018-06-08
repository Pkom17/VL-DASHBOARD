<?php
define("ERROR", 0);
define("SUCCESS", 1);

class ViralLoadAPI {
    private $db;
    private $request_path;
    private $request_body;
    private $response;
	
	//list of table names in db 
	private $table_names = array("administrators", "age_bands", "agecategory", "api_usage", "county_age_breakdown", "county_agebreakdown", "county_entrypoint", "county_iprophylaxis", "county_mprophylaxis", "county_rejections", "county_summary", "county_summary_yearly", "countys", "districts", "entry_points", "facilitys", "feedings", "gender", "hei_categories", "hei_validation", "ip_age_breakdown", "ip_agebreakdown", "ip_entrypoint", "ip_iprophylaxis", "ip_mprophylaxis", "ip_rejections", "ip_summary", "ip_summary_yearly", "lab_rejections", "lab_summary", "lablogs", "labs", "migrations", "national_age_breakdown", "national_agebreakdown", "national_entrypoint", "national_iprophylaxis", "national_mprophylaxis", "national_rejections", "national_summary", "national_summary_yearly", "partners", "password_resets", "patients", "patients_eid", "pcrtype", "platforms", "prophylaxis", "prophylaxistypes", "provinces", "receivedstatus", "rejectedreasons", "results", "rht_samples", "shortcodequeries", "site_rejections", "site_summary", "site_summary_yearly", "sites", "subcounty_age_breakdown", "subcounty_agebreakdown", "subcounty_entrypoint", "subcounty_iprophylaxis", "subcounty_mprophylaxis", "subcounty_rejections", "subcounty_summary", "subcounty_summary_yearly", "survey", "survey_details", "surveyors", "testtype", "user_types", "users", "view_facilitys", "vipin_test_view", "viraljustifications", "viralpmtcttype", "viralprophylaxis", "viralrejectedreasons", "viralsampletype", "viralsampletypedetails", "vl_county_age", "vl_county_gender", "vl_county_justification", "vl_county_pmtct", "vl_county_regimen", "vl_county_rejections", "vl_county_sampletype", "vl_county_summary", "vl_county_summary_view", "vl_lab_platform", "vl_lab_rejections", "vl_lab_sampletype", "vl_lab_summary", "vl_lab_summary_view", "vl_national_age", "vl_national_gender", "vl_national_justification", "vl_national_pmtct", "vl_national_regimen", "Tables_in_apidb 	 ", "vl_national_rejections", "vl_national_sampletype", "vl_national_summary", "vl_national_summary_view", "vl_partner_age", "vl_partner_gender", "vl_partner_justification", "vl_partner_pmtct", "vl_partner_regimen", "vl_partner_rejections", "vl_partner_sampletype", "vl_partner_summary", "vl_partner_summary_view", "vl_site_age", "vl_site_gender", "vl_site_justification", "vl_site_patient_tracking", "vl_site_pmtct", "vl_site_regimen", "vl_site_rejections", "vl_site_sampletype", "vl_site_summary", "vl_site_summary_view", "vl_site_suppression", "vl_site_suppression_year", "vl_subcounty_age", "vl_subcounty_gender", "vl_subcounty_justification", "vl_subcounty_pmtct", "vl_subcounty_regimen", "vl_subcounty_rejections", "vl_subcounty_sampletype", "vl_subcounty_summary", "vl_subcounty_summary_view");
    
	
    function __construct() { 
        $this->connect_db();
        $this->set_headers();
    }
    
    function __destruct() {
        $this->db = null;
    }
    
	//route request to the correct method based on HTTP Method used
    function route_request() {
        $this->request_path = trim($_SERVER['PATH_INFO'],'/');
        $this->request_body = file_get_contents('php://input');
        $method = $_SERVER['REQUEST_METHOD'];
		ob_start();

        file_put_contents('logs/php_api.log', PHP_EOL . "New request received:" . date(DATE_ATOM ) . PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents('logs/php_api.log', "method: " . $method . PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents('logs/php_api.log', "path: " . $this->request_path . PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents('logs/php_api.log', "query string: " . $_SERVER['QUERY_STRING'] . PHP_EOL, FILE_APPEND | LOCK_EX);
        file_put_contents('logs/php_api.log', "body: " . $this->request_body . PHP_EOL, FILE_APPEND | LOCK_EX);
        switch ($method) {
            case 'GET':
                $this->doGet();
                break;
            case 'PUT':
                $this->doPut();
                break;
            case 'POST':
                $this->doPost();
                break;
            case 'DELETE':
                $this->doDelete();
                break;
            default:
                break;
        }
    }
    
	//get a resource in the database
    function doGet() 
    {
		$path_array = explode('/', $this->request_path);
        $table = $path_array[0];
		//ensure table is safe before appending it to sql queries
		if ($this->validTableName($table)) {
		
			//get by query string
			if ($_GET) {
				parse_str($_SERVER['QUERY_STRING'], $params);
				$sql_select = "select * from $table where ";
				$prepend_value = "";
				$index = 0;
				$values = array();
				foreach ($params as $columnName => $value) {
					//ensure column name is safe before appending
					if ($this->validColumnName($columnName)) {
						$sql_select .= $prepend_value . $columnName . " = :value" . $index;
						$prepend_value = " AND ";
						$values[":value" . $index] = $value; 
						++$index;
					} else {
						file_put_contents('logs/php_api.log', 'doGet error > trying to insert into invalid table_column' . PHP_EOL, FILE_APPEND | LOCK_EX);
						ob_clean();
						$response["success"] = ERROR;
						$response["message"] = "Request contains invalid column name";
						die(json_encode($response));
					}
				}
				file_put_contents('logs/php_api.log', 'doGet >' . var_export($sql_select, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
				try {
					$stmt = $this->db->prepare($sql_select);
					$result = $stmt->execute($values);
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
					file_put_contents('logs/php_api.log', 'doGet > result = ' . var_export($result, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
					$response["success"] = SUCCESS;
					$response["message"] = $results;
					die(json_encode($response));
				}
				catch (PDOException $ex) {
					file_put_contents('logs/php_api.log', 'doGet exception >' . var_export($ex, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
					ob_clean();
					$response["success"] = ERROR;
					$response["message"] = "exception during sql";
					die(json_encode($response));
				}
			//get by id	
			} else {
				$id = $path_array[1];
				$sql_select = "select * from $table where id = :id";

				file_put_contents('logs/php_api.log', 'doGet >' . var_export($sql_select, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
				
				try {
					$stmt = $this->db->prepare($sql_select);
					$result = $stmt->execute([':id' => $id]);
					$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
					file_put_contents('logs/php_api.log', 'doGet > result = ' . var_export($result, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
					$response["success"] = SUCCESS;
					$response["message"] = $results;
					die(json_encode($response));
				}
				catch (PDOException $ex) {
					file_put_contents('logs/php_api.log', 'doGet exception >' . var_export($ex, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
					ob_clean();
					$response["success"] = ERROR;
					$response["message"] = "exception during sql";
					die(json_encode($response));
				}
				ob_clean();
				$response["success"] = ERROR;
				$response["message"] = "Unsupported request query";
				die(json_encode($response));
			}
			
		} else {
			file_put_contents('logs/php_api.log', 'doGet error > Requested resource does not exist' . PHP_EOL, FILE_APPEND | LOCK_EX);
			ob_clean();
			$response["success"] = ERROR;
			$response["message"] = "Resource does not exist";
			die(json_encode($response));
		}
		
    }

	//Add a resource to the database
    function doPost() 
    {
        $path_array = explode('/', $this->request_path);
        $body_decode = json_decode($this->request_body, true);
        
        $table = $path_array[0];
        $table_columns = array_keys($body_decode);
        $table_values = array_values($body_decode);
        file_put_contents('logs/php_api.log', "table= " . $table . PHP_EOL, FILE_APPEND | LOCK_EX);
        //file_put_contents('logs/php_api.log', "table_columns= " . var_export($table_columns, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
        //file_put_contents('logs/php_api.log', "table_values= " . var_export($table_values, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
		
		//ensure table is safe before appending it to sql queries
		if ($this->validTableName($table)) {
			
			$sql_insert = "insert into $table (";
			$prepend_value = "";
			foreach ($table_columns as $column) {
				//ensure column name is safe before appending
				if ($this->validColumnName($column)) {
					$sql_insert .= $prepend_value . $column;
					$prepend_value = ", ";
				} else {
					file_put_contents('logs/php_api.log', 'doGet error > trying to insert into invalid table_column' . PHP_EOL, FILE_APPEND | LOCK_EX);
					ob_clean();
					$response["success"] = ERROR;
					$response["message"] = "Request contains invalid column name";
					die(json_encode($response));
				}
			}
			$sql_insert .= ") values (";
			$prepend_value = "";
			$index = 0;
			$values = array();
			foreach ($table_values as $value) {
				$sql_insert .= $prepend_value . "'" . $value . "'";
				$prepend_value = ", ";
				$values[":value" . $index] = $value; 
				++$index;
			}
			$sql_insert .= ")";

			file_put_contents('logs/php_api.log', 'insert1 >' . var_export($sql_insert, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
	  
			try {
				$stmt = $this->db->prepare($sql_insert);
				$result = $stmt->execute($values);
		
				file_put_contents('logs/php_api.log', 'insert2 >' . var_export($result, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
			}
			catch (PDOException $ex) {
				file_put_contents('logs/php_api.log', 'insert exception >' . var_export($ex, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
				ob_clean();
				$response["success"] = ERROR;
				$response["message"] = "exception during sql";
				die(json_encode($response));
			}
		
			$id = $this->db->lastInsertId();
			
			$response["success"] = SUCCESS;
			$response["message"] = array("id"=>$id);
			die(json_encode($response));
			
		} else {
			file_put_contents('logs/php_api.log', 'doGet error > Requested resource does not exist' . PHP_EOL, FILE_APPEND | LOCK_EX);
			ob_clean();
			$response["success"] = ERROR;
			$response["message"] = "Resource does not exist";
			die(json_encode($response));
		}
    }

	//update an already existing resource
    function doPut() 
    {        
        $path_array = explode('/', $this->request_path);
        $body_decode = json_decode($this->request_body, true);
        
        $table = $path_array[0];
		$id = $path_array[1];
        file_put_contents('logs/php_api.log', "table= " . $table . PHP_EOL, FILE_APPEND | LOCK_EX);
		
		//ensure table is safe before appending it to sql queries
		if ($this->validTableName($table)) {
			
			$sql_update = "update $table set ";
			$prepend_value = "";
			$index = 0;
			$values = array();
			foreach ($body_decode as $column => $value) {
				//ensure column name is safe before appending
				if ($this->validColumnName($column)) {
					$sql_update .= $prepend_value . $column . "=" . $value;
					$prepend_value = ", ";
					$values[":value" . $index] = $value; 
					++$index;
				} else {
					file_put_contents('logs/php_api.log', 'doGet error > trying to insert into invalid table_column' . PHP_EOL, FILE_APPEND | LOCK_EX);
					ob_clean();
					$response["success"] = ERROR;
					$response["message"] = "Request contains invalid column name";
					die(json_encode($response));
				}
			}
			$sql_update .= " where id = " . $id;

			file_put_contents('logs/php_api.log', 'update1 >' . var_export($sql_update, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
	  
			try {
				$stmt = $this->db->prepare($sql_update);
				$result = $stmt->execute($values);
		
				file_put_contents('logs/php_api.log', 'update2 >' . var_export($result, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
			}
			catch (PDOException $ex) {
				file_put_contents('logs/php_api.log', 'update exception >' . var_export($ex, true) . PHP_EOL, FILE_APPEND | LOCK_EX);
				ob_clean();
				$response["success"] = ERROR;
				$response["message"] = "exception during sql";
				die(json_encode($response));
			}
			
			$response["success"] = SUCCESS;
			$response["message"] = "Entered resource into $table";
			die(json_encode($response));
		
		} else {
			file_put_contents('logs/php_api.log', 'doGet error > Requested resource does not exist' . PHP_EOL, FILE_APPEND | LOCK_EX);
			ob_clean();
			$response["success"] = ERROR;
			$response["message"] = "Resource does not exist";
			die(json_encode($response));
		}
    }

	//delete a resource from the database
    function doDelete() 
    {
		ob_clean();
        $response["success"] = ERROR;
        $response["message"] = "Unsupported request type";
        die(json_encode($response));
    }
    
    function set_headers() 
	{
        header('Content-Type: text/html; charset=utf-8'); 
    }
    
    function connect_db() 
	{
        // These variables define the connection information for your MySQL database 
        $username = "root"; 
        $password = "Admin"; 
        $host = "localhost"; 
        $dbname = "apidb"; 
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
        $this->db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); 
        
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
    }
	
	//check if provided table name is acceptable (current method: whitelist - secure)
	function validTableName($table_name) 
	{
		return in_array($table_name, $this->table_names);
	}
	
	//check if provided column name is acceptable (current method: whitelist characters - mostly secure)
	function validColumnName($columnName)
	{
		if (preg_match('/^[a-zA-Z0-9_]+/', $columnName)) {
			return true;
		} else {
			file_put_contents('logs/php_api.log', 'invalid column name ' . $columnName .  PHP_EOL, FILE_APPEND | LOCK_EX);
			return false;
		}
	}
    
}

$vl_api = new ViralLoadAPI();
$vl_api->route_request();

?>