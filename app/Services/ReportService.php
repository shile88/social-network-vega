<?php

namespace App\Services;

use App\Models\Report;

class ReportService
{

    /**
     * Get paginated reports.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        return Report::query()->paginate(10);
    }

    /**
     * Store a new report for a specific model.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $model
     * @return \App\Models\Report
     */
    public function store($request, $model)
    {
        $validatedData = $request->validated();

        return $model->reports()
            ->firstOrCreate(
                ['reason' => $validatedData['reason']],
                [
                    'reportable_type' => get_class($model),
                    'reportable_id' => $model->id,
                    'status' => 'pending'
                ],
            );
    }

    /**
     * Show a specific report.
     *
     * @param \App\Models\Report $report
     * @return \App\Models\Report
     */
    public function show($report)
    {
        return $report;
    }

    /**
     * Update the status of a specific report.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Report $report
     * @return \App\Models\Report|false
     */
    public function update($request, $report)
    {
        // Validate the request
        $validatedData = $request->validated();

        if ($validatedData['status'] != $report->status) {
            // Update the report status
            $report->update([
                'status' => $validatedData['status'],
            ]);
            return $report;
        }
        return false;
    }

    /**
     * Delete a specific report.
     *
     * @param \App\Models\Report $report
     * @return bool
     */
    public function destroy($report)
    {
        return $report->delete();
    }
}
