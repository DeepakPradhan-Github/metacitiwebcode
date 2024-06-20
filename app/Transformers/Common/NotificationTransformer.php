<?php

namespace App\Transformers\Common;

use Carbon\Carbon;
use App\Transformers\Transformer;
use App\Models\User;
use App\Models\Admin\Notification;
use App\Models\Admin\UserDriverNotification;

class NotificationTransformer extends Transformer {
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected array $availableIncludes = [

    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Notification $notification) {
        return [
            'id' => $notification->id,
            'title' => $notification->title,
            'body' => $notification->body,
        ];
    }

}
