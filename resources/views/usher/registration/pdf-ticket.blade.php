<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ZURIW25 Conference Ticket: {{ $ticket->ticket_number }}</title>
    <style>
        @page {
            size: 360px 500px;
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 8pt;
            line-height: 1.2;
            background-color: white;
        }
        .ticket-container {
            width: 360px;
            height: 500px;
            position: relative;
            overflow: hidden;
            background-color: white;
            border: 1px solid #ddd;
        }
        .ticket {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .ticket-header {
            background-color: #041E42;
            color: white;
            padding: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left {
            display: flex;
            align-items: center;
        }
        .header-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
        }
        .header-separator {
            color: rgba(255,255,255,0.5);
            margin: 0 6px;
            font-size: 8pt;
        }
        .header-subtitle {
            font-size: 8pt;
            color: rgba(255,255,255,0.8);
            margin: 0;
        }
        .header-date {
            font-size: 8pt;
            color: rgba(255,255,255,0.8);
        }
        .ticket-body {
            padding: 12px;
            position: relative;
        }
        .info-columns {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .info-column {
            flex: 1;
        }
        .info-column.right {
            text-align: right;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-size: 7pt;
            color: #666;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 8pt;
            color: #333;
            font-weight: 500;
        }
        .info-value.ticket-number {
            font-size: 9pt;
            font-weight: bold;
            color: #041E42;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7pt;
            font-weight: 500;
        }
        .badge-blue {
            background-color: #e3f2fd;
            color: #0d47a1;
        }
        .badge-green {
            background-color: #e8f5e9;
            color: #1b5e20;
        }
        .badge-purple {
            background-color: #f3e5f5;
            color: #6a1b9a;
        }
        .badge-red {
            background-color: #ffebee;
            color: #d32f2f;
        }
        .contact-info {
            border-top: 1px solid #eee;
            padding-top: 8px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            font-size: 7pt;
        }
        .contact-item {
            color: #666;
        }
        .contact-value {
            color: #333;
            margin-left: 4px;
        }
        .days-section {
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        .days-title {
            font-size: 7pt;
            color: #666;
            margin-bottom: 6px;
        }
        .days-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }
        .day-item {
            display: flex;
            align-items: center;
            font-size: 7pt;
        }
        .day-icon {
            width: 8px;
            height: 8px;
            margin-right: 4px;
            color: #4caf50;
        }
        .day-name {
            font-weight: 500;
        }
        .day-tag {
            margin-left: 4px;
            padding: 1px 4px;
            border-radius: 4px;
            font-size: 6pt;
            background-color: #e8f5e9;
            color: #1b5e20;
        }
        .ticket-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #f8f9fa;
            padding: 6px 12px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            font-size: 6pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket">
            <!-- Ticket Header -->
            <div class="ticket-header">
                <div class="header-left">
                    <div class="header-title">ZURIW25</div>
                    <span class="header-separator">|</span>
                    <div class="header-subtitle">Official Ticket</div>
                </div>
                <div class="header-date">{{ date('F j, Y') }}</div>
            </div>
            
            <!-- Ticket Body -->
            <div class="ticket-body">
                <!-- Main Info Columns -->
                <div class="info-columns">
                    <!-- Left Column -->
                    <div class="info-column">
                        <div class="info-item">
                            <div class="info-label">Ticket No.</div>
                            <div class="info-value ticket-number">{{ $ticket->ticket_number }}</div>
                    </div>
                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value">{{ $ticket->participant->full_name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Category</div>
                            <div class="info-value">
                                @php
                                    $categories = [
                                        'general' => 'General',
                                        'invited' => 'Invited',
                                        'internal' => 'Internal',
                                        'coordinators' => 'Coordinator'
                                    ];
                                @endphp
                                <span class="badge badge-blue">
                                    {{ $categories[$ticket->participant->category] ?? ucfirst($ticket->participant->category) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="info-column right">
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                <span class="badge badge-green">VALID</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Role</div>
                            <div class="info-value">{{ $ticket->participant->role }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Payment</div>
                            <div class="info-value">
                                @if($ticket->participant->payment_status == 'Not Paid')
                                    <span class="badge badge-red">Not Paid</span>
                                @elseif($ticket->participant->payment_status == 'Complimentary')
                                    <span class="badge badge-purple">Complimentary</span>
                                @elseif($ticket->participant->payment_status == 'Waived' || $ticket->participant->payment_status == 'Not Applicable')
                                    <span class="badge badge-blue">{{ $ticket->participant->payment_status }}</span>
                                @else
                                    <span class="badge badge-green">{{ $ticket->participant->payment_status }}</span>
                        @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Info -->
                <div class="contact-info">
                    <div class="contact-item">
                        Email: <span class="contact-value">{{ $ticket->participant->email }}</span>
                            </div>
                    <div class="contact-item">
                        Phone: <span class="contact-value">{{ $ticket->participant->phone_number }}</span>
                    </div>
                </div>
                
                <!-- Attendance Days -->
                <div class="days-section">
                    <div class="days-title">Valid for:</div>
                    <div class="days-grid">
                        @foreach($conferenceDays as $day)
                            @php
                                $isValid = false;
                                if ($day->id == 1 && $ticket->day1_valid) {
                                    $isValid = true;
                                } elseif ($day->id == 2 && $ticket->day2_valid) {
                                    $isValid = true;
                                } elseif ($day->id == 3 && $ticket->day3_valid) {
                                    $isValid = true;
                                }
                            @endphp
                            @if($isValid)
                                <div class="day-item">
                                    <svg class="day-icon" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="day-name">{{ $day->name }}</span>
                                    @if($day->date->isToday())
                                        <span class="day-tag">Today</span>
                                @endif
                        </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Ticket Footer -->
            <div class="ticket-footer">
                <div>Valid only for dates shown</div>
            </div>
        </div>
    </div>
</body>
</html> 