<?php

class ApiController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default API Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'ApiController@confirmRegistration');
	|
	*/

	public function confirmRegistration()
	{
		//Get registration number and session	
		$reg_num = Input::get('reg_no', '12');
		$session_id = Input::get('session_id');

		//Check if user is registered
		return $this->fetchBigQuery($reg_num);
	}
	
	public function fetchBigQuery($reg_num)
	{	
		// Include google-php-client files
		
		require_once 'Google/Client.php';
		require_once 'Google/Service/Bigquery.php';
		
		// Initiate client class
		$client = new Google_Client();
		// Set application name from the config
		$client->setApplicationName(Config::get('app.google_client_api.app_name'));
		// Create Bigquery service
		$bigqueryService = new Google_Service_Bigquery($client);
		
		// Authorise Bigquery actions
		$key = file_get_contents(Config::get('app.google_client_api.key_location'));
		$cred = new Google_Auth_AssertionCredentials(
			Config::get('app.google_client_api.service_account_email'),
			array('https://www.googleapis.com/auth/bigquery.readonly'),
			$key
		);
		$client->setAssertionCredentials($cred);
		
		// Create query
		$query = new Google_Service_Bigquery_QueryRequest();
		$query->setQuery('SELECT * FROM ['.
			Config::get('app.google_client_api.bigquery.dataset').'.'.
			Config::get('app.google_client_api.bigquery.voter_table').
			'] WHERE id ='.$reg_num);
		
		// Push query and get response
		$jobs = $bigqueryService->jobs;
		$response = $jobs->query(Config::get('app.google_client_api.project_id'), $query);
		
		// Check if voter is in list
		if($response['totalRows']==0){
			$response = null;
		}else{
			$response = $this->fetchBigQuery_formatResponse($response['rows']['0']['f']);
			
			// Save user accessed
			$user = User::firstOrNew(array('voter_id' => $reg_num));
			$user->access_count = $user->access_count + 1;
			$user->save();
		}

		return $response;
	}
	
	// Format response for Google Bigquery
	public function fetchBigQuery_formatResponse($response)
	{
		$user = array();
		$user['voterid'] = $response[0]['v'];
		$user['firstname'] = $response[1]['v'];
		$user['lastname'] = $response[2]['v'];
		$user['district'] = $response[3]['v'];
		$user['constituency'] = $response[4]['v'];
		$user['ward'] = $response[5]['v'];
		$user['regcenter'] = $response[6]['v'];
		$message = array();
		$message['session_id'] = '123afda123e';
		$message['message'] = 'Confirmed. You are registered.';
		return $message;
	}

}