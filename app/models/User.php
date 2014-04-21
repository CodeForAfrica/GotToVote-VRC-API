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
		
		// Update SMS
		if ($data['access_type'] == 'sms') {
			$sms = Sms::find($data['sms_id']);
			$sms->success = true;
			$sms->user_id = $user->id;
			$sms->save();
		}
		
		// Update Web
		if ($data['access_type'] == 'web') {
			$web = Web::find($data['web_id']);
			$web->success = true;
			$web->user_id = $user->id;
			$web->save();
		}
		
		$job->delete();
		
		return 0;
	}


}