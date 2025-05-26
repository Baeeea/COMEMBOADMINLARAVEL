<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', $this->getDashboardData());
    }

    public function refreshData()
    {
        return response()->json($this->getDashboardData());
    }

    private function getDashboardData()
    {
        return [
            'totalResidents' => $this->getTotalResidents(),
            'totalDocuments' => $this->getTotalDocuments(),
            'totalComplaints' => $this->getTotalComplaints(),
            'totalFeedbacks' => $this->getTotalFeedbacks(),
            'recentActivities' => $this->getRecentActivities(),
            'recentUpdates' => $this->getRecentUpdates(),
            'admins' => $this->getAdmins()
        ];
    }

    private function getTotalResidents()
    {
        try {
            return DB::table('residents')->count();
        } catch (\Exception $e) {
            Log::error('Error getting total residents: ' . $e->getMessage());
            return 0;
        }
    }

    private function getTotalDocuments()
    {
        try {
            return DB::table('documentrequest')->where('status', 'pending')->count();
        } catch (\Exception $e) {
            Log::error('Error getting total documents: ' . $e->getMessage());
            return 0;
        }
    }

    private function getTotalComplaints()
    {
        try {
            // Direct query to get all pending complaints
            $query = "SELECT COUNT(*) as count FROM complaintrequests WHERE status = 'pending'";
            $result = DB::select($query);
            return $result[0]->count;
        } catch (\Exception $e) {
            Log::error('Error getting total complaints: ' . $e->getMessage());
            return 0;
        }
    }

    private function getTotalFeedbacks()
    {
        try {
            return DB::table('feedback')->count();
        } catch (\Exception $e) {
            Log::error('Error getting total feedbacks: ' . $e->getMessage());
            return 0;
        }
    }

    private function getRecentActivities()
    {
        try {
            $documentRequests = DB::table('documentrequest')
                ->select(
                    'firstname',
                    'lastname',
                    DB::raw("'Document Request' as category"),
                    'document_type as type',
                    'timestamp'
                )
                ->orderBy('timestamp', 'desc')
                ->limit(5)
                ->get();

            $complaintRequests = DB::table('complaintrequests')
                ->select(
                    'firstname',
                    'lastname',
                    DB::raw("'Complaint' as category"),
                    'complaint_type as type',
                    'timestamp'
                )
                ->orderBy('timestamp', 'desc')
                ->limit(5)
                ->get();

            return $documentRequests->concat($complaintRequests)
                ->sortByDesc('timestamp')
                ->take(5)
                ->values();
        } catch (\Exception $e) {
            Log::error('Error getting recent activities: ' . $e->getMessage());
            return collect();
        }
    }

    private function getRecentUpdates()
    {
        try {
            $news = DB::table('news')
                ->select(
                    DB::raw("'News' as category"),
                    'Title as type',
                    'createdAt as timestamp'
                )
                ->orderBy('createdAt', 'desc')
                ->limit(5)
                ->get();

            $announcements = DB::table('announcements')
                ->select(
                    DB::raw("'Announcement' as category"),
                    'title as type',
                    'createdAt as timestamp'
                )
                ->orderBy('createdAt', 'desc')
                ->limit(5)
                ->get();

            return $news->concat($announcements)
                ->sortByDesc('timestamp')
                ->take(5)
                ->values();
        } catch (\Exception $e) {
            Log::error('Error getting recent updates: ' . $e->getMessage());
            return collect();
        }
    }

    private function getAdmins()
    {
        try {
            return DB::table('users')
                ->where('role', 'admin')
                ->orWhere('role', 'super_admin')
                ->select('name', 'role as position')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting admins: ' . $e->getMessage());
            return collect();
        }
    }
}
