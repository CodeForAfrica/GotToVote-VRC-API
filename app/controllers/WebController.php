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
		$voter_id = Input::get('reg_no');
		$voter_id = trim($voter_id);
		
		// Save Raw Web
		$web = new Web;
		$web->voter_id = $voter_id;
		$web->ip_address = Request::getClientIp();
		$web->success = false;
		$web->user_id = 0;
		$web->save();
		
		// Validate Message Body
		$validator = Validator::make(
		    array('reg_no' => $voter_id),
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
		
		if (Cache::has('vr_'.$voter_id))
		{
			// Voter found
			
			// Save user accessed
			Queue::push('User@QueueSave', array(
				'voter_id' => $voter_id,
				'web_id' => $web->id,
				'access_type' => 'web'
			));
			
			// Get voter center
			$center = DB::table('voting_centers')->where('center_code', Cache::get('vr_'.$voter_id)[1])->first();
			if ( $center == NULL ) {
				$center = (object) array(
					'center_name' => '[No Name]',
					'center_code' => Cache::get('vr_'.$voter_id)[1]
				);
			}
			$center_info = $center->center_code."-".$center->center_name;
			
			// Get name
			$fname = Cache::get('vr_'.$voter_id)[3];
			$sname = Cache::get('vr_'.$voter_id)[2];
			if ($fname == '') {
				$fname = 'BLANK';
			}
			if ($sname == '') {
				$sname = 'BLANK';
			}
			$name = substr($fname, 0, 1).'. '.$sname;
			
			// Send response message
			$voter_id_masked = 'XXXX'.substr($voter_id, -5);
			$response_msg = $voter_id_masked.' Confirmed. '.$name.' is registered to vote at '.$center_info.'.';
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