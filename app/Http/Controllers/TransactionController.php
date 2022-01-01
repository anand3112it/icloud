<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{FinancialTrans, FinancialTransDetail, CommonFeeCollection, CommonFeeCollectionHeadWise};
use Validator;
use Exception;
use DB;

class TransactionController extends Controller
{
    public function index()
    {
        return view('transaction.list')->with([
            'recordsObj' => FinancialTrans::orderBy('id', 'DESC')->paginate(10),
        ]);
    }

    public function details()
    {
        $recordsObj = FinancialTransDetail::select('id', 'financialtranid', 'amount', 'head_name')->orderBy('id', 'DESC')->paginate(10);

        return view('transaction.sublist')->with([
            'recordsObj' => $recordsObj,
            'records' => $this->mapDetails($recordsObj->toArray()),
        ]);
    }

    public function mapDetails($rows)
    {
        if (!empty($rows['data'])) {
            $details = FinancialTrans::select('id', 'roll_no', 'tranid', 'tranDate', 'acadYear')->whereIn('id', array_unique(array_column($rows['data'], 'financialtranid')))->get()->toArray();
            $details_map = !empty($details)?array_column($details, NULL, 'id'):array();

            foreach ($rows['data'] as $ind => $info) {
                $trans = $details_map[$info['financialtranid']] ?? array();

                $rows['data'][$ind]['roll_no'] = $trans['roll_no'] ?? 0;
                $rows['data'][$ind]['tranid'] = $trans['tranid'] ?? 0;
                $rows['data'][$ind]['tranDate'] = $trans['tranDate'] ?? '';
                $rows['data'][$ind]['acadYear'] = $trans['acadYear'] ?? '';
            }
        }

        return $rows;
    }
}
