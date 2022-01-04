<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Branch, FeeCategory, FeeCollectionType, Module, EntryMode, FeeTypes, FinancialTrans, FinancialTransDetail, CommonFeeCollection, CommonFeeCollectionHeadWise, TransactionLog};
use Validator;
use Exception;
use DB;

class ImportController extends Controller
{
    private $totalCount = 0;
    private $successCount = 0;
    private $failedCount = 0;
    private $patchNo = 0;

    public function index()
    {
        return view('import.create');
    }

    public function store(Request $request)
    {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '-1');

        $validator = Validator::make($request->all(), [
            'batch' => 'required|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $this->readCsv($request->file('batch')->getRealPath());

        return redirect()->back()->with('message', 'Batch successfully');
    }

    public function readCsv($path, $delimiter = ',')
    {
        $handle = fopen($path, 'r');

        $module = Module::select('id', 'module_name', 'module_id')->get()->toArray();
        $entry_mode = EntryMode::select('id', 'entry_modename', 'crdr', 'entrymodeno')->get()->toArray();

        $module_map = array_column($module, NULL, 'module_name');
        $entry_mode_map = array_column($entry_mode, NULL, 'entry_modename');

        $this->setPatchNo();

        $result = array();
        if ($handle !== false) {
            $data = array();
            $i = 0;
            while ($row = fgetcsv($handle, 1000, $delimiter)) {
                $this->totalCount++;
                if ($this->totalCount <= 6) {
                    continue;
                }

                $i++;
                $data[] = $row;

                if ($i >= 1000) {
                    $result[] = $this->processData($data, $module_map, $entry_mode_map);

                    $i = 0;
                    $data = array();
                }
            }

            if (!empty($data)) {
                $result[] = $this->processData($data, $module_map, $entry_mode_map);
            }
        }

        fclose($handle);

        return $result;
    }

    public function processData($batchRecords, $module, $entry_mode)
    {
        $status = $message = '';
        $data = $error = array();

        try {
            $this->createLog($batchRecords);

            $branch = $this->fetchBranch($batchRecords);
            if (empty($branch)) {
                throw new Exception('Branch fetch failed');
            }

            $fees_category = $this->fetchFeesCategory($batchRecords);
            if (empty($fees_category)) {
                throw new Exception('Fees category fetch failed');
            }

            $fees_collection_type = $this->fetchFeesCollectionType();
            if (empty($fees_collection_type)) {
                throw new Exception('Fees collection type fetch failed');
            }

            $fees_type = $this->fetchFeesTypes($batchRecords, $fees_category, $fees_collection_type, $module);
            if (empty($fees_type)) {
                throw new Exception('Fees type fetch failed');
            }

            $transaction = $this->createTransaction($batchRecords, $branch, $fees_category, $fees_collection_type, $fees_type, $module, $entry_mode);

            $this->createTransactionDetails($batchRecords, $transaction, $branch, $fees_type, $entry_mode);

            $common_fees_collection = $this->createCommonFeesCollection($batchRecords, $branch, $entry_mode);

            $this->createCommonFeesCollectionHeadWise($batchRecords, $common_fees_collection, $branch, $fees_type, $entry_mode, $module);

            $status = 'success';
            $message = 'Batch data success';
        } catch (Exception $e) {
            $status = 'error';
            if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                $error['common'] = 'Server Issue';
            } elseif ($e->getCode() == '1000') { 
                $error = unserialize($e->getMessage());
            } else {
                $error['common'] = $e->getMessage();
            }
        }
        
        return ['status' => $status, 'message' => $message, 'error' => $error, 'data' => $data];
    }

    public function fetchBranch(array $records)
    {
        $unique_branch = array_filter(array_unique(array_column($records, 11)));
        if (empty($unique_branch)) {
            return [];
        }

        $branchObj = new Branch();

        $branch_result = $branchObj->getAll($unique_branch);
        $branch_map_result = !empty($branch_result)?array_column($branch_result, 'id', 'name'):array();

        $new = array();
        foreach ($unique_branch as $branch_name) {
            if (!isset($branch_map_result[$branch_name])) {
                $new[] = [
                    'name' => $branch_name,
                ];
            }
        }

        if (empty($new)) {
            return $branch_map_result;
        }

        Branch::insert($new);

        $branch_result = $branchObj->getAll($unique_branch);

        return !empty($branch_result)?array_column($branch_result, 'id', 'name'):array();
    }

    public function fetchFeesCategory(array $records)
    {
        $unique_fees_category = array_filter(array_unique(array_column($records, 10)));
        if (empty($unique_fees_category)) {
            return [];
        }

        $feesCatgoryObj = new FeeCategory();
        $map_fee_category_result = $feesCatgoryObj->mapFeesCategory($feesCatgoryObj->getAll($unique_fees_category));

        $branch = (new Branch)->getAll();

        $new = array();
        foreach ($unique_fees_category as $fees_category_name) {
            foreach ($branch as $branch_info) {
                if (!isset($map_fee_category_result[$branch_info['id']][$fees_category_name])) {
                    $new[] = [
                        'fee_category' => $fees_category_name,
                        'br_id' => $branch_info['id'],
                    ];
                }
            }
        }

        if (empty($new)) {
            return $map_fee_category_result;
        }

        FeeCategory::insert($new);

        return $feesCatgoryObj->mapFeesCategory($feesCatgoryObj->getAll($unique_fees_category));
    }

    public function fetchFeesCollectionType()
    {
        $branch = (new Branch)->getAll();

        $obj = new FeeCollectionType();
        $map_result = $obj->mapFeesCollectionType($obj->getAll());

        $fees_types = ['academic', 'academicmisc', 'hostel', 'hostelmisc', 'transport', 'transportmisc'];
        $new = array();
        foreach ($fees_types as $types) {
            foreach ($branch as $info) {
                if (!isset($map_result[$info['id']][$types])) {
                    $new[] = [
                        'collectionhead' => $types,
                        'collectiondesc' => $types,
                        'br_id' => $info['id'],
                    ];
                }
            }
        }

        if (empty($new)) {
            return $map_result;
        }

        FeeCollectionType::insert($new);

        return $obj->mapFeesCollectionType($obj->getAll());
    }

    public function fetchFeesTypes(array $records, $fees_category, $collection_type, $module)
    {
        $unique_fee_type = array_filter(array_unique(array_column($records, 16)));
        if (empty($unique_fee_type)) {
            return [];
        }

        $fee_category_map = array_column($records, 10, 16);

        $obj = new FeeTypes();
        $map_fee_type = $obj->mapFeesType($obj->getAll($unique_fee_type));

        $branch = (new Branch)->getAll();

        $new = array();
        foreach ($unique_fee_type as $fees_type) {
            foreach ($branch as $branch_info) {
                if (!isset($map_fee_type[$branch_info['id']][$fees_type])) {
                    $category = $fee_category_map[$fees_type] ?? 0;

                    $new[] = [
                        'fee_category' => $fees_category[$branch_info['id']][$category] ?? 0,
                        'f_name' => $fees_type,
                        'br_id' => $branch_info['id'],
                        'fee_type_ledger' => $fees_type,
                        'collection_id' => $collection_type[$branch_info['id']][$this->getModuleName($fees_type)] ?? 0,
                    ];
                }
            }
        }

        if (empty($new)) {
            return $map_fee_type;
        }

        FeeTypes::insert($new);

        return $obj->mapFeesType($obj->getAll($unique_fee_type));
    }

    public function createTransaction($records, $branch, $fees_category, $fees_collection_type, $fees_type, $module, $entry_mode)
    {
        $dt = date('Y-m-d H:i:s');
        $client_ip = request()->ip();

        $map_records = array();
        foreach ($records as $info) {
            if (empty($info[6])) {
                continue;
            }

            if (isset($map_records[$info[6]])) {
                $map_records[$info[6]]['amount'] += ($info[18] ?? 0);
                $map_records[$info[6]]['due_amount'] += ($info[17] ?? 0);
                $map_records[$info[6]]['concession_amount'] += ($info[19] ?? 0);
                $map_records[$info[6]]['duerev'] += ($info[22] ?? 0);
            } else {
                $voucher_type = strtolower($info[5]);

                $map_records[$info[6]] = [
                    'roll_no' => $info[7] ?? 0,
                    'moduleid' => 1,
                    'tranid' => $info[6] ?? 0,
                    'amount' => $info[18] ?? 0,
                    'crdr' => $entry_mode[$voucher_type]['crdr'] ?? 'NOT_INDENTIFIED',
                    'tranDate' => !empty($info[1])?date('Y-m-d', strtotime($info[1].' 01:00:00')):NULL,
                    'acadYear' => $info[2] ?? '',
                    'entrymode' => $entry_mode[$voucher_type]['entrymodeno'] ?? 0,
                    'voucherno' => $info[6] ?? 0,
                    'brid' => $branch[$info[11]] ?? 0,
                    'due_amount' => $info[17] ?? 0,
                    'concession_amount' => $info[19] ?? 0,
                    'duerev' => $info[22] ?? 0,
                    'created_at' => $dt,
                    'created_ip' => $client_ip,
                    'updated_ip' => '',
                ];
            }
        }

        $exist_trans = FinancialTrans::select('id', 'tranid')->whereIn('tranid', array_keys($map_records))->get()->toArray();
        $map_exist_trans_map = !empty($exist_trans)?array_column($exist_trans, 'id', 'tranid'):array();

        $new = $update = array();
        foreach ($map_records as $trans_id => $trans_info) {
            if (isset($map_exist_trans_map[$trans_id])) {
                $temp = array(
                    'amount=(amount + '.(!empty($trans_info['amount'])?$trans_info['amount']:0).')',
                    'due_amount=(due_amount + '.(!empty($trans_info['due_amount'])?$trans_info['due_amount']:0).')',
                    'concession_amount=(concession_amount + '.(!empty($trans_info['concession_amount'])?$trans_info['concession_amount']:0).')',
                    'duerev=(duerev + '.(!empty($trans_info['duerev'])?$trans_info['duerev']:0).')',
                    'updated_at=\''.$dt.'\'',
                    'updated_ip=\''.$client_ip.'\'',
                );

                $update[] = 'UPDATE financial_trans SET '.implode(',', $temp).' WHERE id=\''.$map_exist_trans_map[$trans_id].'\'';
            } else {
                $new[] = $trans_info;
            }
        }
        
        if (!empty($new)) {
            FinancialTrans::insert($new);
        }

        if (!empty($update)) {
            $chunk = array_chunk($update, 500);
            foreach ($chunk as $update_array) {
                DB::unprepared(implode(';', $update_array));
            }
        }

        $exist_trans = FinancialTrans::select('id', 'tranid')->whereIn('tranid', array_keys($map_records))->get()->toArray();

        return !empty($exist_trans)?array_column($exist_trans, 'id', 'tranid'):array();
    }

    public function createTransactionDetails($records, $transaction, $branch, $fees_type, $entry_mode)
    {
        $new = array();
        foreach ($records as $info) {
            $voucher_type = strtolower($info[5]);
            $branch_id = $branch[$info[11]] ?? 0;

            $new[] = [
                'paid_date' => !empty($info[1])?date('Y-m-d', strtotime($info[1].' 00:00:00')):NULL,
                'financialtranid' => $transaction[$info[6]] ?? 0,
                'moduleid' => 1,
                'amount' => $info[18] ?? 0,
                'headid' => $fees_type[$branch_id][$info[16]] ?? 0,
                'crdr' => $entry_mode[$voucher_type]['crdr'] ?? 'NOT_INDENTIFIED',
                'br_id' => $branch_id,
                'head_name' => $info[16],
                'csv_record_details' => addslashes(json_encode($info)),
            ];
        }

        return FinancialTransDetail::insert($new);
    }

    public function createCommonFeesCollection($records, $branch, $entry_mode)
    {
        $dt = date('Y-m-d H:i:s');
        $client_ip = request()->ip();

        $map_records = array();
        foreach ($records as $info) {
            if (empty($info[6])) {
                continue;
            }

            if (isset($map_records[$info[6]])) {
                $map_records[$info[6]]['amount'] += ($info[18] ?? 0);
            } else {
                $map_records[$info[6]] = [
                    'moduleid' => 1,
                    'transid' => $info[6] ?? 0,
                    'admno' => $info[8] ?? '',
                    'rollno' => $info[7] ?? 0,
                    'amount' => $info[18] ?? 0,
                    'brid' => $branch[$info[11]] ?? 0,
                    'acadamicyear' => $info[2] ?? '',
                    'financialyear' => $info[3] ?? '',
                    'displayreceiptno' => $info[15] ?? '',
                    'entrymode' => $entry_mode[strtolower($info[5])]['entrymodeno'] ?? 0,
                    'paiddate' => !empty($info[1])?date('Y-m-d H:i:s', strtotime($info[1].' 01:00:00')):NULL,
                ];
            }
        }

        $exist_trans = CommonFeeCollection::select('id', 'transid')->whereIn('transid', array_keys($map_records))->get()->toArray();
        $map_exist_trans_map = !empty($exist_trans)?array_column($exist_trans, 'id', 'transid'):array();

        $new = $update = array();
        foreach ($map_records as $trans_id => $trans_info) {
            if (isset($map_exist_trans_map[$trans_id])) {
                $temp = array(
                    'amount=(amount + '.(!empty($trans_info['amount'])?$trans_info['amount']:0).')',
                );

                $update[] = 'UPDATE common_fee_collection SET '.implode(',', $temp).' WHERE id=\''.$map_exist_trans_map[$trans_id].'\'';
            } else {
                $new[] = $trans_info;
            }
        }

        if (!empty($new)) {
            CommonFeeCollection::insert($new);
        }

        if (!empty($update)) {
            $chunk = array_chunk($update, 500);
            foreach ($chunk as $update_array) {
                DB::unprepared(implode(';', $update_array));
            }
        }

        $exist_trans = CommonFeeCollection::select('id', 'transid')->whereIn('transid', array_keys($map_records))->get()->toArray();

        return !empty($exist_trans)?array_column($exist_trans, 'id', 'transid'):array();
    }

    public function createCommonFeesCollectionHeadWise($records, $transaction, $branch, $fees_type, $entry_mode, $module)
    {
        $new = array();
        foreach ($records as $info) {
            $voucher_type = strtolower($info[5]);
            $branch_id = $branch[$info[11]] ?? 0;

            $new[] = [
                'paid_date' => !empty($info[1])?date('Y-m-d', strtotime($info[1].' 00:00:00')):NULL,
                'moduleid' => $module[$this->getModuleName($info[16] ?? '999')]['module_id'] ?? 0,
                'receiptid' => $transaction[$info[6]] ?? 0,
                'headid' => $fees_type[$branch_id][$info[16]] ?? 0,
                'headname' => $info[16],
                'br_id' => $branch_id,
                'amount' => $info[18] ?? 0,
            ];
        }

        return CommonFeeCollectionHeadWise::insert($new);
    }

    public function getModuleName($fee_types)
    {
        if (in_array($fee_types, ['TUITION FEE', 'Exam Fee', 'Security Fee', 'Ajustable_Excess_Amount', 'Adjusted_Amount', 'Reckecking/Scrutiny Fee', 'Exam Fee (CemeCter)', 'Degree/Convocation/Certificate fee', 'Training & Certification Fee', 'Exam Fee Debarred', 'Tuition Fee Debarred', 'Exam Fees Back Paper', 'Exam Fee (Letral Deploma)', 'Registration Fee', 'Tuition Fee (IBM Classes)', 'Thesis Fees', 'Exam Fees', 'Tuition Fees', 'Excess Amount', 'Degree Fees', 'Degree Fee'])) {
            return 'academic';
        } elseif (in_array($fee_types, ['Fine Fee', 'Adjustable Excess Fee', 'Tuition Fee (Back Paper)', 'Tuition Fee (IBM ClaCCeC)', 'Library Fine Fee', 'Exam Fee (Back Paper)', 'Library BookC Recieved', 'Sport Activity Received', 'Exam Fees Debarred Paper', 'Tution Fees debarred paper', 'Convocation Fee Head', 'Student ID Fee', 'Library Books Recieved', 'Special Backlog fee', 'Registration FIne Even Sem', 'Online Registration Fine odd Sem', 'Revaluation Fee', 'Rechecking Fee', 'Indisciplinary Fine', 'Exam Fee ET Eligibilty', 'Online Registration Fine even Sem', 'Misc Exam Fees Back Paper', 'Exam Fee (Semester)', 'OTHER FEES', 'Tuition Fees', 'Other Fee', 'Registration FIne Odd Sem', 'Student ID Fee MISC'])) {
            return 'academicmisc';
        } elseif (in_array($fee_types, ['Hostel & Mess Fee'])) {
            return 'hostel'; 
        } elseif (in_array($fee_types, [])) {
            return 'hostelmisc';
        } elseif (in_array($fee_types, [])) {
            return 'transport';
        } elseif (in_array($fee_types, ['TRANSPORT FEE'])) {
            return 'transportmisc';
        } else {
            return 'invalid';
        } 
    }

    public function createLog($records)
    {
        if (empty($records)) {
            return false;
        }

        $time = time();
        $client_ip = request()->ip();

        $temp = array();
        foreach ($records as $details) {
            $temp[] = [
                'transaction_date' => !empty($details[1])?date('Y-m-d', strtotime($details[1].' 00:00:00')):NULL,
                'academic_year' => $details[2] ?? '',
                'session_year' => $details[3] ?? '',
                'alloted_category' => $details[4] ?? '',
                'voucher_type' => $details[5] ?? '',
                'voucher_no' => $details[6] ?? '',
                'roll_no' => $details[7] ?? '',
                'adm_no' => $details[8] ?? '',
                'trans_status' => $details[9] ?? '',
                'fee_category' => $details[10] ?? '',
                'faculty' => $details[11] ?? '',
                'program' => !empty($details[12])?htmlentities($details[12]):'',
                'department' => !empty($details[13])?htmlentities($details[13]):'',
                'batch' => !empty($details[14])?htmlentities($details[14]):'',
                'receipt_no' => $details[15] ?? '',
                'fee_head' => $details[16] ?? '',
                'due_amount' => $details[17] ?? '0.00',
                'paid_amount' => $details[18] ?? '0.00',
                'concession_amount' => $details[19] ?? '0.00',
                'scholarship_amount' => $details[20] ?? '0.00',
                'reverse_concession_amount' => $details[21] ?? '0.00',
                'write_off_amount' => $details[22] ?? '0.00',
                'adjusted_amount' => $details[23] ?? '0.00',
                'refund_amount' => $details[24] ?? '0.00',
                'fund_transfer_amount' => $details[25] ?? '0.00',
                'remarks' => !empty($details[26])?htmlentities($details[26]):'',
                'created_at' => $time,
                'created_ip' => $client_ip,
                'batch_no' => $this->patchNo,
            ];
        }

        return TransactionLog::insert($temp);
    }

    public function setPatchNo()
    {
        $last_batch_no = TransactionLog::select('batch_no')->orderByDesc('id')->limit(1)->value('batch_no');

        $last_batch_no = !empty($last_batch_no)?$last_batch_no:0;
        $last_batch_no++;

        $this->patchNo = $last_batch_no;

        return true;
    }
}
