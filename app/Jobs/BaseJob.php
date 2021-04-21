<?php

namespace HDSSolutions\Finpar\Jobs;

use HDSSolutions\Finpar\Http\Middleware\SettingsLoader;
use HDSSolutions\Finpar\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseJob implements ShouldQueue {
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    protected $user;
    protected Company $company;

    /**
     * Register Middleware
     * @return array
     */
    public function middleware() {
        return [ new SettingsLoader ];
    }

    public function __construct() {
        // save user that dispatched the job
        $this->user = auth()->user();
        // save company where we are working
        $this->company = backend()->company();
    }

    protected final function notify(string $eventClass, ...$params) {
        // dispatch notification event
        forward_static_call_array([ $eventClass, 'dispatch' ], array_merge([ $this->user ], $params));
    }

    public final function handle() {
        // register company
        backend()->setCompany( $this->company );
        // execute Job process
        $this->execute();
    }

    protected abstract function execute();

}
