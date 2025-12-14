<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get count of non-repeating customers (customers who have made exactly one purchase or no purchases)
        
        // Get client IDs who have made exactly one purchase
        $singlePurchaseClients = DB::select(
            'SELECT S.clientid, COUNT(*) as purchase_count
             FROM `sale` AS S 
             WHERE S.clientid > 0 
             GROUP BY S.clientid
             HAVING purchase_count = 1'
        );
        
        $singlePurchaseClientIds = [];
        foreach ($singlePurchaseClients as $client) {
            if ($client->clientid > 0) {
                $singlePurchaseClientIds[] = $client->clientid;
            }
        }

        // Get all client IDs who have made any purchase
        $clientsWithSales = DB::select(
            'SELECT DISTINCT S.clientid 
             FROM `sale` AS S 
             WHERE S.clientid > 0'
        );
        
        $clientsWithSalesIds = [];
        foreach ($clientsWithSales as $sale) {
            if ($sale->clientid > 0) {
                $clientsWithSalesIds[] = $sale->clientid;
            }
        }

        // Get all real clients
        $allRealClients = Client::where('isrealclient', '=', '1')->pluck('id')->toArray();
        
        // Non-repeating = clients with exactly one purchase OR clients with no purchases
        $clientsWithNoPurchases = array_diff($allRealClients, $clientsWithSalesIds);
        $nonRepeatingIds = array_merge($singlePurchaseClientIds, $clientsWithNoPurchases);
        
        // Count non-repeating clients
        $nonRepeatingCount = count($nonRepeatingIds);

        return view('home', ['nonRepeatingCount' => $nonRepeatingCount]);
    }

    /**
     * Get popular services based on filter (weekly, monthly, yearly)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPopularServices(Request $request)
    {
        $filter = $request->input('filter', 'monthly'); // weekly, monthly, yearly

        // Build date condition based on filter
        $dateCondition = '';
        switch ($filter) {
            case 'weekly':
                $dateCondition = "AND S.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'monthly':
                $dateCondition = "AND S.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
            case 'yearly':
                $dateCondition = "AND S.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;
            default:
                $dateCondition = "AND S.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        }

        // Query to get popular services with count and total quantity
        $popularServices = DB::select("
            SELECT 
                SI.itemid,
                COALESCE(MAX(SVC.name), MAX(SI.title), 'Unknown Service') as service_name,
                SUM(SI.quantity) as total_quantity,
                COUNT(DISTINCT SI.saleid) as total_sales,
                SUM(SI.actualpriceperitem * SI.quantity) as total_revenue
            FROM saleitems SI
            INNER JOIN sale S ON SI.saleid = S.id
            LEFT JOIN services SVC ON SI.itemid = SVC.id AND SI.itemtype = 'service'
            WHERE SI.itemtype = 'service'
            {$dateCondition}
            GROUP BY SI.itemid
            ORDER BY total_quantity DESC
            LIMIT 10
        ");

        return response()->json([
            'success' => true,
            'data' => $popularServices,
            'filter' => $filter
        ]);
    }
}
