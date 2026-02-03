<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportCoaAccountsRequest;
use App\Services\CoaAccountImportService;
use Illuminate\Http\JsonResponse;

class CoaAccountImportController extends Controller
{
    public function __construct(
        protected CoaAccountImportService $importService,
    ) {}

    /**
     * Validate imported rows and return per-row errors.
     */
    public function validate(ImportCoaAccountsRequest $request): JsonResponse
    {
        $rows = $request->validated()['rows'];
        $errors = $this->importService->validate($rows);

        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors,
        ]);
    }

    /**
     * Import validated rows into the database.
     */
    public function import(ImportCoaAccountsRequest $request): JsonResponse
    {
        $rows = $request->validated()['rows'];

        // Re-validate before importing to catch race conditions
        $errors = $this->importService->validate($rows);

        if (! empty($errors)) {
            return response()->json([
                'success' => false,
                'errors' => $errors,
            ], 422);
        }

        $count = $this->importService->import($rows);

        return response()->json([
            'success' => true,
            'message' => "{$count} account(s) imported successfully.",
            'count' => $count,
        ]);
    }
}
