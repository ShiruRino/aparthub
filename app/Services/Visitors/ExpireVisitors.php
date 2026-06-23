<?php

namespace App\Services\Visitors;

use App\Models\Visitor;
use Illuminate\Support\Carbon;

class ExpireVisitors
{
    public function run(?Carbon $now = null): int
    {
        $now ??= now();

        return Visitor::query()
            ->whereIn('status', [Visitor::STATUS_PENDING, Visitor::STATUS_APPROVED])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->update([
                'status' => Visitor::STATUS_EXPIRED,
                'updated_at' => $now,
            ]);
    }

    public function applyToVisitor(Visitor $visitor, ?Carbon $now = null): Visitor
    {
        $now ??= now();

        if ($visitor->isExpired($now) && in_array($visitor->status, [Visitor::STATUS_PENDING, Visitor::STATUS_APPROVED], true)) {
            $visitor->forceFill([
                'status' => Visitor::STATUS_EXPIRED,
            ])->save();
        }

        return $visitor->refresh();
    }
}
