<?php

namespace App\Http\Controllers;

use App\Http\Support\DatabaseSupporter;
use App\Http\Support\Supporter;
use App\Libs\HttpStatusCode;
use App\Libs\JsonResponse;
use App\Libs\MessageCode;
use App\ViewModels\ActivityViewModel;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    use JsonResponse;

    protected $supporter;
    protected $activityViewModel;
    protected $databaseSupporter;

    public function __construct()
    {
        $this->supporter = new Supporter();
        $this->activityViewModel = new ActivityViewModel();
        $this->databaseSupporter = new DatabaseSupporter();
    }

    public function getMyPosts(Request $request)
    {
        try
        {
            $authorId = auth()->user()->id;
            $allQuestionsSql = $this->activityViewModel->doGetMyPostsSQL($authorId);

            $allQuestions = $this->databaseSupporter->getPaginatedDBData($allQuestionsSql, $request);

            $manipulatedQuestions = $this->activityViewModel->doManipulateDataMyPosts($allQuestions);

            $dataResponse = $this->supporter->getArrayResponseListPagination($manipulatedQuestions, $request);

            return $this->standardJsonResponse(
                HttpStatusCode::SUCCESS_OK,
                true,
                MessageCode::msgSuccess('my_posts'),
                $dataResponse
            );
        }
        catch(\Exception $exception)
        {
            return $this->standardJsonExceptionResponse($exception);
        }
    }
}
