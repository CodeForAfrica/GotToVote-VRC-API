<?php

class Voter extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'VOTER_TABLE';

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
			'] WHERE registration_number ='.$reg_num);
		
		// Push query and get response
		$jobs = $bigqueryService->jobs;
		$response = $jobs->query(Config::get('app.google_client_api.project_id'), $query);

		return $response;
	}

}