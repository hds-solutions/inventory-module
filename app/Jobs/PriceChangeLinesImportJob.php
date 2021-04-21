<?php

namespace HDSSolutions\Finpar\Jobs;

use HDSSolutions\Finpar\Imports\PriceChangeLinesImporter as Importer;
use HDSSolutions\Finpar\Models\Currency;
use HDSSolutions\Finpar\Models\File;
use HDSSolutions\Finpar\Models\PriceChange;
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
        //
        if (!($this->currency instanceof Currency)) $this->currency = Currency::findOrFail($this->currency);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
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
        // delete imported file
        $this->import->delete();
    }
}
