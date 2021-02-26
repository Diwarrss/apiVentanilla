<?php

namespace App\Console\Commands;

use App\EntryFiling;
use App\OutgoingFiling;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $entry_filings = EntryFiling::join('type_documents', 'entry_filings.type_document_id', '=', 'type_documents.id')
        ->select('entry_filings.created_at', 'entry_filings.id', 'type_documents.days')
        ->where('entry_filings.state', 1)
        ->get();
      foreach ($entry_filings as $key => $value) {
        $days = $this->changeStatus($value);
        $diff = $value->days - $days;
        if ($diff < 1) {
          $entry_filing = EntryFiling::find($value->id);
          $entry_filing->state = 3;
          $entry_filing->save();
        }
      }

      $outgoing_filings = OutgoingFiling::join('type_documents', 'outgoing_filings.type_document_id', '=', 'type_documents.id')
        ->select('outgoing_filings.created_at', 'outgoing_filings.id', 'type_documents.days')
        ->where('outgoing_filings.state', 1)
        ->get();
      foreach ($outgoing_filings as $key => $value) {
        $days = $this->changeStatus($value);
        $diff = $value->days - $days;
        if ($diff < 1) {
          $outgoing_filing = OutgoingFiling::find($value->id);
          $outgoing_filing->state = 3;
          $outgoing_filing->save();
        }
      }
    }

    public function changeStatus($value){
      // fecha 1
      $fecha_dada= date($value['created_at']);
      // fecha actual
      $fecha_actual= date("Y/m/d h:m:s");
      $dias = (strtotime($fecha_dada)-strtotime($fecha_actual))/86400;
      $dias = abs($dias); $dias = floor($dias);
      return $dias + 1;
    }
}
