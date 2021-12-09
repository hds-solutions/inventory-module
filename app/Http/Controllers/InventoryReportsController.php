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
use HDSSolutions\Laravel\Models\PriceList;

class InventoryReportsController extends Controller {

    public function stock(Request $request, StockReport $report) {
        // force company selection
        if (!backend()->companyScoped()) return view('backend::layouts.master', [ 'force_company_selector' => true ]);

        // load resources
        if ($request->ajax()) return $report->ajax();

        // load filters
        $types = Type::ordered()->get();
        $brands = Brand::ordered()->with([
            'models' => fn($model) => $model->ordered(),
        ])->get();
        $families = Family::ordered()->with([
            'subFamilies' => fn($subFamily) => $subFamily->ordered(),
        ])->get();
        $lines = Line::ordered()->with([
            'gamas' => fn($gama) => $gama->ordered(),
        ])->get();
        $purchase_price_lists = PriceList::ordered()->isPurchase()->get();
        $sale_price_lists = PriceList::ordered()->isSale()->get();

        // return view with report
        return $report->render('inventory::reports.stock', compact(
            'types', 'brands', 'families', 'lines',
            'purchase_price_lists', 'sale_price_lists',
        ) + [
            'count'                 => Resource::count(),
            'show_company_selector' => !backend()->companyScoped(),
        ]);
    }

}
