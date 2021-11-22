<?php

namespace HDSSolutions\Laravel\Jobs;

use HDSSolutions\Laravel\Imports\InventoryLinesImporter as Importer;
use HDSSolutions\Laravel\Models\Inventory;
use HDSSolutions\Laravel\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class InventoryLinesImportJob extends BaseJob {

    public function __construct(
        private Inventory $resource,
        private Collection $matches,
        private File $import,
        private bool $diff = false
    ) {
        parent::__construct();
    }

    public function execute() {
        logger('Import of Excel '.$this->import->name.' to '.$this->resource->description.' started');

        // start a transaction
        DB::beginTransaction();

        // instanciate importer
        $importer = new Importer($this->resource, $this->matches,
            // send diff flag
            diff: $this->diff,
        );
        // import document to model
        Excel::import($importer, $this->import->file());

        // confirm transaction
        DB::commit();

        // TODO: notify user that event has finished
        // $this->notify( InventoryLinesImportFinished::class, $this->resource );

        // delete imported file
        $this->import->delete();
        logger('Import of Excel '.$this->import->name.' to '.$this->resource->description.' finished');
    }

}
