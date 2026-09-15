<?php

namespace App\Console\Commands;
use App\Http\Controllers\ApplicationReceiptController;
use Illuminate\Console\Command;

class EMIReminder extends Command {

	public $emiReminderCtrl;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'emi:reminder';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(ApplicationReceiptController $emiReminderCtrl) {
		parent::__construct();
		$this->emiReminderCtrl = $emiReminderCtrl;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$this->emiReminderCtrl->cronSMS();
	}
}
