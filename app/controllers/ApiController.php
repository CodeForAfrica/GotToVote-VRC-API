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
		$reg_num = Input::get('reg_no');
		$session_id = Input::get('session_id');

		//Check if user is registered
		$user = $this->voterinfo->get_user($reg_num);

		if($user!=null){
			//set sucess message 
			$message = $reg_num." Confirmed. You are registered to vote at ".$user['regcenter'].". Remember to carry your National I.D card when going to vote.";
			//log to database
			$this->voterinfo->save_user($user);	
		}else{
			//set fail message
			$message = "Sorry, we are unable to verify your Registration No. ".$reg_num.". Please check and try again";
		}
		//send message
		//redirect($api_url."?message=".$message."&sessionid=".$sessionid);
		print $message;
		return View::make('hello');
	}

}