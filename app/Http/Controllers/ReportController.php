<?php

namespace HDSSolutions\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use HDSSolutions\Laravel\Reports\StockReport;
use HDSSolutions\Laravel\Http\Request;
use HDSSolutions\Laravel\Models\Locator as Resource;
use HDSSolutions\Laravel\Models\Family;
use HDSSolutions\Laravel\Models\Line;
use HDSSolutions\Laravel\Models\Type;
use HDSSolutions\Laravel\Models\Brand;

class ReportController extends Controller {

    public function stock(Request $request, StockReport $report) {
        // load resources
        if ($request->ajax()) return $report->ajax();

        // load filters
        $types = Type::ordered()->get();
        $brands = Brand::with([
            'models' => fn($model) => $model->ordered(),
        ])->ordered()->get();
        $families = Family::with([
            'subFamilies' => fn($subFamily) => $subFamily->ordered(),
        ])->ordered()->get();
        $lines = Line::with([
            'gamas' => fn($gama) => $gama->ordered(),
        ])->ordered()->get();

        // return view with report
        return $report->render('inventory::reports.stock', compact(
            'types', 'brands', 'families', 'lines',
        ) + [
            'count'                 => Resource::count(),
            'show_company_selector' => !backend()->companyScoped(),
        ]);
    }

}
