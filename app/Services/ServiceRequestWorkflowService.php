<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\Models\ServiceRequestEvent;
use App\Models\TechnicianTeam;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServiceRequestWorkflowService
{
    public function assignToTeam(ServiceRequest $ticket, TechnicianTeam $team, ?string $scheduledAt, ?User $actor = null, ?string $notes = null): ServiceRequest
    {
        return DB::transaction(function () use ($ticket, $team, $scheduledAt, $actor, $notes) {
            $fromStatus = $ticket->status;

            $ticket->fill([
                'technician_team_id' => $team->id,
                'assigned_to' => $team->name,
                'scheduled_at' => $scheduledAt ?: null,
                'status' => ServiceRequest::STATUS_ASSIGNED,
                'assigned_at' => $ticket->assigned_at ?? now(),
            ])->save();

            $this->logEvent($ticket, 'assigned_to_team', $fromStatus, ServiceRequest::STATUS_ASSIGNED, $actor, $notes, [
                'team_id' => $team->id,
                'team_name' => $team->name,
                'scheduled_at' => $ticket->scheduled_at?->toIso8601String(),
            ]);

            return $ticket->fresh(['resident.unit', 'categoryMaster', 'subcategory', 'technicianTeam', 'attachments', 'events.actor']);
        });
    }

    public function markOnTheWay(ServiceRequest $ticket, User $actor, int $estimatedArrivalMinutes): ServiceRequest
    {
        $this->guardTechnicianCanOperate($ticket, $actor);

        if (! in_array($ticket->status, [ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_ON_THE_WAY], true)) {
            throw ValidationException::withMessages([
                'status' => 'Ticket hanya bisa diubah ke On The Way dari status Assigned.',
            ]);
        }

        return DB::transaction(function () use ($ticket, $actor, $estimatedArrivalMinutes) {
            $fromStatus = $ticket->status;

            $ticket->fill([
                'status' => ServiceRequest::STATUS_ON_THE_WAY,
                'on_the_way_at' => $ticket->on_the_way_at ?? now(),
                'estimated_arrival_minutes' => $estimatedArrivalMinutes,
            ])->save();

            $this->logEvent($ticket, 'on_the_way', $fromStatus, ServiceRequest::STATUS_ON_THE_WAY, $actor, null, [
                'estimated_arrival_minutes' => $estimatedArrivalMinutes,
            ]);

            return $ticket->fresh(['resident.unit', 'categoryMaster', 'subcategory', 'technicianTeam', 'attachments', 'events.actor']);
        });
    }

    /**
     * @param  array<int, UploadedFile>  $beforePhotos
     */
    public function start(ServiceRequest $ticket, User $actor, array $beforePhotos): ServiceRequest
    {
        $this->guardTechnicianCanOperate($ticket, $actor);

        if (! in_array($ticket->status, [ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_ON_THE_WAY], true)) {
            throw ValidationException::withMessages([
                'status' => 'Ticket hanya bisa dimulai dari status Assigned atau On The Way.',
            ]);
        }

        if ($beforePhotos === []) {
            throw ValidationException::withMessages([
                'before_photos' => 'Foto before wajib diunggah sebelum memulai pengerjaan.',
            ]);
        }

        $existingBeforeCount = $ticket->attachments()
            ->where('attachment_type', ServiceRequestAttachment::TYPE_TECHNICIAN_BEFORE)
            ->count();

        if (($existingBeforeCount + count($beforePhotos)) > 3) {
            throw ValidationException::withMessages([
                'before_photos' => 'Maksimal 3 foto before diperbolehkan.',
            ]);
        }

        return DB::transaction(function () use ($ticket, $actor, $beforePhotos) {
            foreach ($beforePhotos as $photo) {
                $this->storeAttachment($ticket, $photo, ServiceRequestAttachment::TYPE_TECHNICIAN_BEFORE, $actor);
            }

            $fromStatus = $ticket->status;

            $ticket->fill([
                'status' => ServiceRequest::STATUS_IN_PROGRESS,
                'in_progress_at' => $ticket->in_progress_at ?? now(),
            ])->save();

            $this->logEvent($ticket, 'start_progress', $fromStatus, ServiceRequest::STATUS_IN_PROGRESS, $actor, null, [
                'before_photo_count' => count($beforePhotos),
            ]);

            return $ticket->fresh(['resident.unit', 'categoryMaster', 'subcategory', 'technicianTeam', 'attachments', 'events.actor']);
        });
    }

    /**
     * @param  array<int, UploadedFile>  $afterPhotos
     */
    public function complete(ServiceRequest $ticket, User $actor, array $afterPhotos, string $completionNotes): ServiceRequest
    {
        $this->guardTechnicianCanOperate($ticket, $actor);

        if ($ticket->status !== ServiceRequest::STATUS_IN_PROGRESS) {
            throw ValidationException::withMessages([
                'status' => 'Ticket hanya bisa diselesaikan dari status In Progress.',
            ]);
        }

        if (trim($completionNotes) === '') {
            throw ValidationException::withMessages([
                'completion_notes' => 'Catatan penyelesaian wajib diisi.',
            ]);
        }

        if ($afterPhotos === []) {
            throw ValidationException::withMessages([
                'after_photos' => 'Foto after wajib diunggah sebelum menyelesaikan ticket.',
            ]);
        }

        $existingAfterCount = $ticket->attachments()
            ->where('attachment_type', ServiceRequestAttachment::TYPE_TECHNICIAN_AFTER)
            ->count();

        if (($existingAfterCount + count($afterPhotos)) > 3) {
            throw ValidationException::withMessages([
                'after_photos' => 'Maksimal 3 foto after diperbolehkan.',
            ]);
        }

        return DB::transaction(function () use ($ticket, $actor, $afterPhotos, $completionNotes) {
            foreach ($afterPhotos as $photo) {
                $this->storeAttachment($ticket, $photo, ServiceRequestAttachment::TYPE_TECHNICIAN_AFTER, $actor);
            }

            $fromStatus = $ticket->status;

            $ticket->fill([
                'status' => ServiceRequest::STATUS_COMPLETED,
                'completion_notes' => $completionNotes,
                'completed_at' => now(),
            ])->save();

            $this->logEvent($ticket, 'complete', $fromStatus, ServiceRequest::STATUS_COMPLETED, $actor, $completionNotes, [
                'after_photo_count' => count($afterPhotos),
            ]);

            return $ticket->fresh(['resident.unit', 'categoryMaster', 'subcategory', 'technicianTeam', 'attachments', 'events.actor']);
        });
    }

    public function logEvent(
        ServiceRequest $ticket,
        string $eventType,
        ?string $fromStatus,
        ?string $toStatus,
        ?User $actor = null,
        ?string $notes = null,
        array $meta = []
    ): ServiceRequestEvent {
        return $ticket->events()->create([
            'acted_by_user_id' => $actor?->id,
            'event_type' => $eventType,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'meta' => $meta === [] ? null : $meta,
        ]);
    }

    public function storeAttachment(ServiceRequest $ticket, UploadedFile $file, string $type, ?User $actor = null): ServiceRequestAttachment
    {
        $path = $file->store('service-requests/attachments', 'public');

        return $ticket->attachments()->create([
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'file_size' => $file->getSize(),
            'attachment_type' => $type,
            'uploaded_by_user_id' => $actor?->id,
        ]);
    }

    public function guardTechnicianCanOperate(ServiceRequest $ticket, User $actor): void
    {
        if (! $actor->isTechnician() || ! $actor->activeForApi()) {
            throw ValidationException::withMessages([
                'technician' => 'Akun technician tidak aktif atau tidak valid.',
            ]);
        }

        if (! $ticket->technicianTeam || ! $actor->technicianTeams->contains('id', $ticket->technician_team_id)) {
            throw ValidationException::withMessages([
                'ticket' => 'Ticket tidak terhubung ke team technician Anda.',
            ]);
        }
    }
}
