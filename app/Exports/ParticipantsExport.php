<?php

namespace App\Exports;

use App\Models\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ParticipantsExport
{
    protected $roles = null;

    /**
     * Create a new export instance.
     *
     * @param array|null $roles
     * @return void
     */
    public function __construct($roles = null)
    {
        $this->roles = $roles;
    }

    /**
     * Download the participants as a CSV file
     *
     * @param string $fileName
     * @return \Illuminate\Http\Response
     */
    public function download($fileName = 'participants.csv')
    {
        try {
            // Create the file in memory
            $csv = $this->generateCsv();
            
            // Return the file as a download
            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            \Log::error('Export Error: ' . $e->getMessage());
            return Response::json(['error' => 'Could not export data: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Generate CSV content
     * 
     * @return string
     */
    private function generateCsv()
    {
        $output = fopen('php://temp', 'r+');
        
        // Add BOM to fix UTF-8 in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add headers
        fputcsv($output, [
            'ID',
            'Full Name',
            'Email',
            'Phone Number',
            'Role',
            'Organization',
            'Payment Status',
            'Amount Paid',
            'Checked In',
            'Registration Date'
        ]);
        
        // Get all participants (simplified approach without chunking)
        $participants = $this->getParticipants();
        
        foreach ($participants as $participant) {
            fputcsv($output, [
                $participant->id ?? 'N/A',
                $participant->full_name ?? 'N/A',
                $participant->email ?? 'N/A',
                $participant->phone_number ?? 'N/A',
                ucfirst($participant->role ?? 'Unknown'),
                $participant->organization ?? 'N/A',
                ucfirst($participant->payment_status ?: 'Pending'),
                $participant->payment_amount ? 'KES ' . number_format($participant->payment_amount, 2) : 'N/A',
                isset($participant->check_ins_count) && $participant->check_ins_count > 0 ? 'Yes' : 'No',
                $participant->created_at ? $participant->created_at->format('Y-m-d H:i:s') : 'N/A'
            ]);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
    
    /**
     * Get participants based on roles
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getParticipants()
    {
        $query = Participant::query()
            ->select([
                'participants.*',
                DB::raw('(SELECT COUNT(*) FROM check_ins WHERE check_ins.participant_id = participants.id) as check_ins_count')
            ])
            ->orderBy('created_at', 'desc');
            
        if ($this->roles) {
            $query->whereIn('role', $this->roles);
        }
        
        return $query->get();
    }
}
