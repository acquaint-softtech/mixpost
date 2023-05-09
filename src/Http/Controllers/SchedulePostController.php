<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Inovector\Mixpost\Facades\Settings;
use Inovector\Mixpost\Http\Requests\SchedulePost;

class SchedulePostController extends Controller
{
    public function __invoke(SchedulePost $schedulePost): JsonResponse
    {
        $schedulePost->handle();

        $scheduledAt = $schedulePost->getDateTime()->tz(Settings::query()->get('timezone'))->format("D, M j, " . timeFormat());

        return response()->json("The post has been scheduled.\n$scheduledAt");
    }
}
