<?php

namespace App\Observers;

use App\Models\Report;
use Illuminate\Support\Facades\Log;

class ReportObserver
{
    public function updated(Report $report)
    {
        // Check if the status has been updated to "accepted"
        if ($report->isDirty('status') && $report->status === 'accepted') {

            // Get the related reportable model
            $reportable = $report->reportable;
            // Update the status of the reportable model to "banned"
            if ($reportable) {
                $reportable->status = 'banned';
                $reportable->save();
            }
        }

        $reportable = $report->reportable;
        $reportable->status = 'banned';
        $reportable->save();
    }
}
