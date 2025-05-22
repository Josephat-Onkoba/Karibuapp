<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;

class ParticipantController extends Controller
{
    /**
     * Display the categories overview page
     */
    public function index()
    {
        return view('admin.participants.index');
    }
    
    /**
     * Display the category specific participant list
     */
    public function category($category)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        // Will implement participant filtering later
        $participants = []; // Participant::where('category', $category)->paginate(15);
        
        // Get the title for the category
        $titles = [
            'general' => 'General Participants',
            'invited' => 'Invited Guests & Speakers',
            'internal' => 'Internal Participants',
            'coordinators' => 'Session Coordinators'
        ];
        
        $title = $titles[$category];
        $subtitle = $this->getCategoryDescription($category);
        
        return view('admin.participants.category', compact('participants', 'category', 'title', 'subtitle'));
    }
    
    /**
     * Show form to create a new participant
     */
    public function create($category)
    {
        // Validate category
        $validCategories = ['general', 'invited', 'internal', 'coordinators'];
        
        if (!in_array($category, $validCategories)) {
            abort(404);
        }
        
        $title = $this->getCategoryTitle($category);
        $roles = $this->getCategoryRoles($category);
        
        return view('admin.participants.create', compact('category', 'title', 'roles'));
    }
    
    /**
     * Get roles available for each category
     */
    private function getCategoryRoles($category)
    {
        $roles = [
            'general' => ['Delegate', 'Exhibitor', 'Conference Presenter'],
            'invited' => ['Chief Guest', 'Guest', 'Keynote Speaker', 'Panelist'],
            'internal' => ['Staff', 'Student'],
            'coordinators' => ['Secretariat', 'Moderator', 'Rapporteur']
        ];
        
        return $roles[$category];
    }
    
    /**
     * Get category title
     */
    private function getCategoryTitle($category)
    {
        $titles = [
            'general' => 'General Participants',
            'invited' => 'Invited Guests & Speakers',
            'internal' => 'Internal Participants',
            'coordinators' => 'Session Coordinators'
        ];
        
        return $titles[$category];
    }
    
    /**
     * Get category description
     */
    private function getCategoryDescription($category)
    {
        $descriptions = [
            'general' => 'Delegates, Exhibitors, Conference Presenters',
            'invited' => 'Chief Guests, Guests, Keynote Speakers, Panelists',
            'internal' => 'Staff, Students',
            'coordinators' => 'Secretariat, Moderators, Rapporteurs'
        ];
        
        return $descriptions[$category];
    }
    
    /**
     * Show form to import participants
     */
    public function showImport($category)
    {
        $title = $this->getCategoryTitle($category);
        return view('admin.participants.import', compact('category', 'title'));
    }
} 