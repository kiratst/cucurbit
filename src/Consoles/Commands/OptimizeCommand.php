<?php namespace Cucurbit\Framework\Consoles\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class OptimizeCommand extends Command
{
	use ConfirmableTrait;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cucurbit:optimize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generating optimized module cache';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->info('Generating optimized module cache');

		app('cucurbit')->optimize();
	}

}