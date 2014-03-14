<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';
	
	protected $fillable = array('voter_id');
	
	public function QueueSave($job, $data)
	{
		// Save user accessed via queueing system
		
		// Save user accessed
		$user = User::firstOrNew(array('voter_id' => $data['voter_id']));
		$user->access_count = $user->access_count + 1;
		$user->save();
		
		// Save SMS
		$sms = new Sms;
		$sms->message_id = $data['message_id'];
		$sms->session_id = $data['session_id'];
		$sms->message_body = $data['message_body'];
		$sms->date_received = $data['date_received'];
		$sms->message_type = $data['message_type'];
		$sms->user_id = $user->id;
		$sms->save();
		
		$job->delete();
		
		return 0;
	}


}