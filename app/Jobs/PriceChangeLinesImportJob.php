<?php

namespace HDSSolutions\Laravel\Jobs;

use HDSSolutions\Laravel\Imports\PriceChangeLinesImporter as Importer;
use HDSSolutions\Laravel\Models\Currency;
use HDSSolutions\Laravel\Models\File;
use HDSSolutions\Laravel\Models\PriceChange;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class PriceChangeLinesImportJob extends BaseJob {

    public function __construct(
        private PriceChange $resource,
        private Collection $matches,
        private File $import,
        private Currency|int $currency,
        private bool $diff = false
    ) {
        parent::__construct();
        // get Currency model
        if (!($this->currency instanceof Currency)) $this->currency = Currency::findOrFail($this->currency);
    }

    protected function execute() {
        logger('Import of Excel '.$this->import->name.' to '.$this->resource->description.' started');

        // instanciate importer
        $importer = new Importer($this->resource, $this->matches,
            // specify currency
            currency: $this->currency,
            // send diff flag
            diff: $this->diff,
        );
        // import document to model
        Excel::import($importer, $this->import->file());

        // TODO: notify user that event has finished
        // $this->notify( PriceChangeLinesImportFinished::class, $this->resource );

        // delete imported file
        $this->import->delete();
        logger('Import of Excel '.$this->import->name.' to '.$this->resource->description.' finished');
    }

}
