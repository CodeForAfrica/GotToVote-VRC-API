<?php

class LoadController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default API Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::post('/sms', 'SmsController@receiveSMS');
	|
	*/
	
	public function memcache() {
		
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
			']');
		
		// Push query and get response
		$jobs = $bigqueryService->jobs;
		$response = $jobs->query(Config::get('app.google_client_api.project_id'), $query);
		
		foreach ($response->rows as $row) {
			Cache::forever('vr_'.$row['f']['0']['v'], $row);
			//echo print_r(Cache::get('vr_'.$row['f']['0']['v']));
		}
		
		echo 'Cache complete';
	}
	
	public function info() {
		echo phpinfo();
	}

}