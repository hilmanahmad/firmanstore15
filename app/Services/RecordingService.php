<?php

namespace App\Services;

use App\Models\Recording;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RecordingService
{
    protected $recording;

    public function __construct(Recording $recording)
    {
        $this->recording = $recording;
    }

    public function getByArrayDT($rows, $offset, $searchKey, $status = null, $userId = null)
    {
        $query = $this->recording->query();

        // Filter by user if provided (for non-SUPERADMIN)
        if ($userId) {
            $query = $query->where('user_id', $userId);
        }

        if ($status) {
            $query = $query->where('status', $status);
        }

        if ($searchKey) {
            $query = $query->where(function ($q) use ($searchKey) {
                $q->where('code', 'LIKE', '%' . $searchKey . '%')
                    ->orWhere('filename', 'LIKE', '%' . $searchKey . '%')
                    ->orWhere('custom_filename', 'LIKE', '%' . $searchKey . '%');
            });
        }

        if ($rows) {
            return $query->skip($offset)->take($rows)->orderBy('created_at', 'DESC')->get();
        } else {
            return $query->count();
        }
    }

    public function getById($id)
    {
        return $this->recording->where('id', $id)->first();
    }

    public function getByCode($code)
    {
        return $this->recording->where('code', $code)->first();
    }

    public function startRecording($request)
    {
        $code = 'REC-' . date('YmdHis') . '-' . strtoupper(Str::random(4));

        $data = [
            'code' => $code,
            'user_id' => $request->user_id ?? auth()->id(),
            'custom_filename' => $request->custom_filename,
            'status' => 'recording',
            'started_at' => now(),
            'notes' => $request->notes,
        ];

        return $this->recording->create($data);
    }

    public function stopRecording($code, $filename = null, $filePath = null)
    {
        $recording = $this->getByCode($code);

        if (!$recording) {
            return false;
        }

        $stoppedAt = now();
        $duration = $recording->started_at->diffInSeconds($stoppedAt);

        $recording->update([
            'status' => 'stopped',
            'stopped_at' => $stoppedAt,
            'duration' => $duration,
            'filename' => $filename,
            'file_path' => $filePath,
        ]);

        return $recording;
    }

    public function completeRecording($code, $filename, $filePath)
    {
        $recording = $this->getByCode($code);

        if (!$recording) {
            return false;
        }

        $recording->update([
            'status' => 'completed',
            'filename' => $filename,
            'file_path' => $filePath,
        ]);

        return $recording;
    }

    public function failRecording($code, $reason = null)
    {
        $recording = $this->getByCode($code);

        if (!$recording) {
            return false;
        }

        $recording->update([
            'status' => 'failed',
            'notes' => ($recording->notes ? $recording->notes . "\n\n" : '') . 'Error: ' . $reason,
        ]);

        return $recording;
    }

    public function destroy($id)
    {
        $this->recording->where('id', $id)->delete();
    }

    public function getAll()
    {
        return $this->recording->orderBy('created_at', 'DESC')->get();
    }
}
