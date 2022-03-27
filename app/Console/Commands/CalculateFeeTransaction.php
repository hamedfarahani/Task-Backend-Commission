<?php

namespace App\Console\Commands;

use App\Lib\CommissionCalculator;
use App\Transformer\CsvToTransaction;
use Illuminate\Console\Command;

class CalculateFeeTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:transaction {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculate fee transaction';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $lines = $this->readLines();
        foreach ($lines as $line) {
            $csvTransaction = new CsvToTransaction($line);
            $transaction = $csvTransaction($line);
            $calculator = new CommissionCalculator();
            $transaction = $calculator->calculateCommission($transaction);
            $this->info('fee value : ' . $transaction);
        }
    }

    private function readLines(): array
    {
        if (!($file = fopen($this->argument('filename'), 'r'))) {
            throw new \RuntimeException('Failed to open file');
        }

        $lines = [];
        while (is_array($line = fgetcsv($file, null, ','))) {
            $lines[] = $line;
        }

        return $lines;
    }
}
