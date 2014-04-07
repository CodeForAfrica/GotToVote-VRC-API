<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Sms extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'smses';
	
	protected $fillable = array('message_id');

	public function QueueSave($job, $data)
	{	
		// Save SMS
		$sms = new Sms;
		$sms->message_id = $data['message_id'];
		$sms->session_id = $data['session_id'];
		$sms->message_body = $data['message_body'];
		$sms->date_received = $data['date_received'];
		$sms->message_type = $data['message_type'];
		$sms->success = $data['success'];
		$sms->user_id = $data['user_id'];
		$sms->save();
		
		$job->delete();
		
		return 0;
	}
	
}