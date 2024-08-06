<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class LookupController
 *
 * @package App\Http\Controllers
 */
class LookupController extends Controller
{
    protected $services;

    public function __construct()
    {

        $this->services = [
            'minecraft' => app('minecraft'),
            'steam'     => app('steam'),
            'xbl'       => app('xbl'),
        ];
    }

    /**
     * Lookup action on the controller
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function lookup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type'     => 'required|string',
            'username' => 'nullable',
            'id'       => 'nullable',
        ]);

        if ($validator->fails())
        {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        $type     = $validated['type'];
        $username = $validated['username'] ?? null;
        $id       = $validated['id'] ?? null;

        if (!isset($this->services[$type]))
        {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        // Access the service from our available services based on the type provided
        $service = $this->services[$type];

        try
        {
            if ($username !== null)
            {
                return $service->lookupByUsername($username);
            }

            if ($id !== null)
            {
                return $service->lookupById($id);
            }
            else
            {
                return response()->json(['error' => 'Username or ID required'], 400);
            }
        }
        catch (Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
