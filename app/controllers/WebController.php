<?php

class WebController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default API Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::post('/web', 'WebController@checkRegistered');
	|
	*/

	public function checkRegistered()
	{
		// Validate inputs exist
		$rules = array(
			'reg_no' => 'required',
		);
	    $validator = Validator::make(Input::all(), $rules);
	    if ($validator->fails()) {
	    	return Response::json(array(
	    		'success' => 'false',
	    		'message' => $validator->messages()->all())
	    	);
	    }
		
		// Inputs
		$reg_no = Input::get('reg_no');
		
		// Unique session_id and voter_id
		$session_id = uniqid();
		$reg_no = trim($reg_no);
		
		// Validate Message Body
		$validator = Validator::make(
		    array('reg_no' => $reg_no),
		    // Rules
		    array('reg_no' => 
		    	array('alpha_num','min:8|max:8')
		    )
		);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => 'false',
				'message' => 'The registration number is not valid. Please check and send again.'
			));
		}
		
		
		// Fetch Voter from Memcached
		
		if (Cache::has('vr_'.$reg_no))
		{
			// Voter found
			$center = DB::table('voting_centers')->where('center_code', Cache::get('vr_'.$reg_no)[1])->first();
			if ( $center == NULL ) {
				$center = (object) array(
					'center_name' => '0'
				);
			}
			$name = substr(Cache::get('vr_'.$reg_no)[3], 0, 1).'. '.Cache::get('vr_'.$reg_no)[2];
			$voter_id_masked = 'XXXX'.substr($reg_no, -5);
			$response_msg = $voter_id_masked.' Confirmed. '.$name.' is registered to vote at '.$center->center_name.'.';
			return Response::json(array(
				'success' => 'true',
				'message' => $response_msg
			));
		    
		}
		
		// No Voter Found
		return Response::json(array(
			'success' => 'false',
			'message' => 'The registration number cannot be found. Please check and send again.'
		));
		
		
	}

}