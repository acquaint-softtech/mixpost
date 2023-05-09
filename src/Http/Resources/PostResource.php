<?php

namespace Inovector\Mixpost\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Inovector\Mixpost\Enums\PostStatus;
use Inovector\Mixpost\Facades\Settings;

class PostResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status(),
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
            'versions' => PostVersionResource::collection($this->whenLoaded('versions')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'scheduled_at' => [
                'date' => $this->scheduled_at?->tz(Settings::query()->get('timezone'))->toDateString(),
                'time' => $this->scheduled_at?->tz(Settings::query()->get('timezone'))->format('H:i'),
                'human' => $this->scheduled_at?->tz(Settings::query()->get('timezone'))->format("D, M j, " . timeFormat())
            ],
            'published_at' => [
                'human' => $this->published_at?->tz(Settings::query()->get('timezone'))->format("D, M j, " . timeFormat())
            ]
        ];
    }

    protected function status()
    {
        if ($this->isScheduleProcessing()) {
            return 'PUBLISHING';
        }

        return $this->status->name;
    }
}
