<?php

class SmsController extends BaseController {

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

	public function receiveSMS()
	{
		// Validate inputs exist
		$rules = array(
			'message_id' => 'required',
			'message_body' => 'required',
			'date_received' => 'required',
			'message_type' => 'required',
		);
	    $validator = Validator::make(Input::all(), $rules);
	    if ($validator->fails()) {
	    	return Response::json(array('errors' => $validator->messages()->all()));
	    }
		
		// Inputs
		$message_id = Input::get('message_id');
		$message_body = Input::get('message_body');
		$date_received = Input::get('date_received');
		$message_type = Input::get('message_type');
		
		// Unique session_id and voter_id
		$session_id = uniqid();
		$voter_id = trim($message_body);
		
		// Validate Message Body
		$validator = Validator::make(
		    array('voter_id' => $voter_id),
		    // Rules
		    array('voter_id' => 
		    	array('alpha_num','min:8|max:8')
		    )
		);
		if ($validator->fails()) {
			return Response::json(array(
				'message_id' => $message_id,
				'session_id' => $session_id,
				'message_type' => $message_type,
				'message_body' => 'The voter number is not valid. Please check and send again.'
			));
		}
		
		// Fetch Voter from Bigquery
		$voter = new Voter;
		$bigquery = $voter->fetchBigquery($voter_id);
		
		// No Voter Found
		if ($bigquery->totalRows == 0) {
			return Response::json(array(
				'message_id' => $message_id,
				'session_id' => $session_id,
				'message_type' => $message_type,
				'message_body' => 'The voter number cannot be found. Please check and send again.'
			));
		}
		
		// Voter found
		
		// Save user accessed
		Queue::push('User@QueueSave', array(
			'voter_id' => $voter_id,
			'message_id' => $message_id,
			'session_id' => $session_id,
			'message_body' => $message_body,
			'date_received' => $date_received,
			'message_type' => $message_type
		));
		
		
		// Return JSON Response
		return Response::json(array(
			'message_id' => $message_id,
			'session_id' => $session_id,
			'message_type' => $message_type,
			'message_body' => 'Success!'
		));
		

	}

}