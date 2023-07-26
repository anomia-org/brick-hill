<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User\Admin\{
    Report,
    ReportType,
    ReportReason
};

use App\Http\Requests\User\Report\SendReport;
use App\Rules\Validation\PolyType;

class ReportController extends Controller
{
    use \App\Traits\Controllers\PostLimit;

    public function report(Request $request) {
        if(!array_key_exists($request->type, Report::$reportable_types))
            return back();

        return view('pages.admin.report')->with([
            'report_type' => Report::$reportable_types[$request->type],
            'reasons' => ReportReason::all()
        ]);
    }

    public function sendReport(SendReport $request) {
        if(!$this->canMakeNewPost(Auth::user()->sent_reports()))
            throw new \BaseException('You can only make a new report every 30 seconds');
        
        $checkExisting = Report::where([
            'reportable_id' => $request->reportable_id,
            'reportable_type' => $request->reportable_type,
            'seen' => 0
        ])->exists();
        
        if($checkExisting == 0)
            $report = Report::create([
                'user_id' => auth()->id(),
                'report_reason_type_id' => $request->reason,
                'reportable_id' => $request->reportable_id,
                'reportable_type' => $request->reportable_type,
                'report_note' => $request->note,
                'seen' => 0
            ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Your report has been successfully submitted.');
    }
}
