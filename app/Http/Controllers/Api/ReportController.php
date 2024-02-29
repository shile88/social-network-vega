<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostReportRequest;
use App\Http\Requests\StoreUserReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ReportController extends Controller
{
    /**
     * ReportController constructor.
     *
     * @param \App\Services\ReportService $reportService The report service.
     */
    public function __construct(protected ReportService $reportService)
    {
    }

    /**
     * Display a listing of all reports.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing all reports.
     */
    public function index() : JsonResponse
    {
        // Retrieve all reports using the ReportService
        $reports = $this->reportService->index();

        // Return a JSON response with all reports and pagination information
        return response()->json([
            'success' => true,
            'message' => 'All reports',
            'data' => [
                'reports' => ReportResource::collection($reports),
                'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created report for a post in storage.
     *
     * @param \App\Http\Requests\StorePostReportRequest $request The request containing report data.
     * @param \App\Models\Post $post The post to be reported.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of report creation.
     */
    public function storePost(StorePostReportRequest $request, Post $post) : JsonResponse
    {
        $this->authorize('view', $post);
        // Call the common storeReport method with the specified request and post
        return $this->storeReport($request, $post);
    }

    /**
     * Store a newly created report for a user in storage.
     *
     * @param \App\Http\Requests\StoreUserReportRequest $request The request containing report data.
     * @param \App\Models\User $user The user to be reported.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of report creation.
     */
    public function storeUser(StoreUserReportRequest $request, User $user) : JsonResponse
    {
        // Call the common storeReport method with the specified request and user
        return $this->storeReport($request, $user);
    }

    /**
     * Display the specified report.
     *
     * @param \App\Models\Report $report The report to be displayed.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the specified report.
     */
    public function show(Report $report) : JsonResponse
    {
        // Retrieve and return the specified report using the ReportService
        $report = $this->reportService->show($report);

        return response()->json([
            'success' => true,
            'message' => 'Your report',
            'data' => [
                'report' => ReportResource::make($report)
            ]
        ]);
    }

    /**
     * Update the specified report in storage.
     *
     * @param \App\Http\Requests\UpdateReportRequest $request The request containing updated report data.
     * @param \App\Models\Report $report The report to be updated.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of report update.
     */
    public function update(UpdateReportRequest $request, Report $report) : JsonResponse
    {
        // Update the specified report using the ReportService
        $updatedReport = $this->reportService->update($request, $report);

        // Check if the report was not updated with the same status
        return $updatedReport ?
                response()->json([
                    'success' => true,
                    'message' => 'Report successfully updated',
                    'data' => [
                        'report' => ReportResource::make($updatedReport)
                    ]
                ])
            :
                 response()->json([
                     'success' => false,
                     'message' => 'Trying to update with same status',
                     'data' => null
                 ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Remove the specified report from storage.
     *
     * @param \App\Models\Report $report The report to be deleted.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of report deletion.
     */
    public function destroy(Report $report) : JsonResponse
    {
        // Delete the specified report using the ReportService
       $this->reportService->destroy($report);

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully',
            'data' => null
        ]);
    }

    /**
     * Store a newly created report in storage for a model (either post or user).
     *
     * @param \Illuminate\Http\Request $request The request containing report data.
     * @param mixed $model The model (post or user) to be reported.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the success of report creation.
     */
    public function storeReport($request, $model) : JsonResponse
    {
        // Call the ReportService to store the report for the specified model
        $report = $this->reportService->store($request, $model);

        // Check if the report was recently created
        return $report->wasRecentlyCreated ?
            response()->json([
                'success' => true,
                'message' => 'Report created successfully',
                'data' => [
                    'report' => ReportResource::make($report)
                ]
            ], ResponseAlias::HTTP_CREATED)
        :
            response()->json([
                'success' => false,
                'message' => 'Report already exists',
                'data' => [
                    'report' => ReportResource::make($report)
                ]
            ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }
}
