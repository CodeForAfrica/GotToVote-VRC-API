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
		$msg_lang = strtolower(Input::get('lang', 'eng'));
		
		// Unique session_id and voter_id
		$session_id = uniqid();
		$voter_id = trim($message_body);
		
		// Save Raw SMS
		$sms = new Sms;
		$sms->message_id = $message_id;
		$sms->session_id = $session_id;
		$sms->message_body = $message_body;
		$sms->date_received = $date_received;
		$sms->message_type = $message_type;
		$sms->success = false;
		$sms->user_id = 0;
		$sms->lang = $msg_lang;
		$sms->save();
		
		// Validate Message Body
		$validator = Validator::make (
		    array('voter_id' => $voter_id),
		    // Rules
		    array('voter_id' => 
		    	array('alpha_num','min:9','max:9')
		    )
		);
		if ($validator->fails()) {
			$response_msg = "The voter id is not valid. Please check and send again. #5VOTE";
			if ($msg_lang == "chi") {
				$response_msg = "Nambala yovotelayo yalakwika. Chonde tumizaninso ndi nambala yolondola #5VOTE";
			}
			if ($msg_lang == "tum") {
				$response_msg = "Nambala iyo mwatuma yabudika/Mwabudiska nambala iyo mwatuma. Tumizganiso makora #5VOTE";
			}
			
			return Response::json(array(
				'message_id' => $message_id,
				'session_id' => $session_id,
				'message_type' => $message_type,
				'message_body' => $response_msg
			));
		}
		
		
		// Fetch Voter from Memcached
		
		if (Cache::has('vr_'.$voter_id))
		{
			// Voter found
			
			// Save user accessed
			Queue::push('User@QueueSave', array(
				'voter_id' => $voter_id,
				'sms_id'=> $sms->id,
			));
			
			$center = DB::table('voting_centers')->where('center_code', Cache::get('vr_'.$voter_id)[1])->first();
			if ( $center == NULL ) {
				$center = (object) array(
					'center_name' => '0'
				);
			}
			$name = substr(Cache::get('vr_'.$voter_id)[3], 0, 1).'. '.Cache::get('vr_'.$voter_id)[2];
			$voter_id_masked = 'XXXX'.substr($voter_id, -5);
			$response_msg = $voter_id_masked.' Confirmed. '.$name.' is registered to vote at '.$center->center_name.'.';
			if ($msg_lang == "chi") {
				$response_msg = $voter_id_masked." Ndiyolondola. ".$name." muli m’kaundula woponya voti ku ".$center->center_name." #5VOTE";
			}
			if ($msg_lang == "tum") {
				$response_msg = $voter_id_masked." Nambala inu njeniyiyo. ".$name." muli mukaundula wakuponya voti ku ".$center->center_name." #5VOTE";
			}
			
			return Response::json(array(
				'message_id' => $message_id,
				'session_id' => $session_id,
				'message_type' => $message_type,
				'message_body' => $response_msg
			));
		    
		}
		
		// No Voter Found
		$response_msg = "The voter id cannot be found. Please check and send again.";
		if ($msg_lang == "chi") {
			$response_msg = "Nambala yovotela mwatumizayo siyikupezeka. Chonde tumizaninso ndi nambala yolondola #5VOTE";
		}
		if ($msg_lang == "tum") {
			$response_msg = "Nambala inu yakuvotera ikusangika yayi. Yezganiso kutumizga #5VOTE";
		}
		return Response::json(array(
			'message_id' => $message_id,
			'session_id' => $session_id,
			'message_type' => $message_type,
			'message_body' => $response_msg
		));
		
		
	}

}