<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ZURIW25 Conference Ticket: {{ $ticket->ticket_number }}</title>
    <style>
        @page {
            size: 360px 580px;
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
            height: 580px;
            position: relative;
            overflow: hidden;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .ticket {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .ticket-header {
            background: linear-gradient(135deg, #041E42 0%, #0A3366 100%);
            color: white;
            padding: 12px 10px;
            position: relative;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .ticket-logo {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
        }
        .ticket-subtitle {
            font-size: 8pt;
            color: rgba(255,255,255,0.8);
            margin: 2px 0 0 0;
            font-weight: 300;
        }
        .ticket-body {
            padding: 10px;
            position: relative;
            background-color: white;
        }
        .ticket-number-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            position: relative;
        }
        .ticket-number-section {
            flex: 1;
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #eaeaea;
        }
        .ticket-number-label {
            font-size: 7pt;
            color: #666;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .ticket-number {
            font-size: 12pt;
            font-weight: bold;
            color: #041E42;
            letter-spacing: 1px;
        }
        .ticket-qr {
            width: 60px;
            height: 60px;
            background-color: #f1f3f4;
            margin-left: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            border: 1px dashed #ddd;
        }
        .qr-text {
            font-size: 5pt;
            color: #666;
            text-align: center;
        }
        .section {
            margin-bottom: 12px;
            background-color: white;
            border-radius: 4px;
            padding: 8px;
            border: 1px solid #f0f0f0;
        }
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #041E42;
            margin: 0 0 6px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-content {
            padding: 2px 0;
        }
        .info-row {
            margin-bottom: 4px;
            display: flex;
            font-size: 7pt;
        }
        .info-label {
            color: #666;
            width: 75px;
            flex-shrink: 0;
        }
        .info-value {
            font-weight: 500;
            flex-grow: 1;
            color: #333;
        }
        .days-section .section-content {
            display: flex;
            flex-wrap: wrap;
        }
        .day-row {
            width: 100%;
            display: flex;
            align-items: center;
            margin-bottom: 3px;
            font-size: 7pt;
        }
        .day-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 4px;
            font-size: 6pt;
        }
        .day-valid {
            background-color: #e8f5e9;
            color: #1b5e20;
        }
        .day-invalid {
            background-color: #f5f5f5;
            color: #999;
        }
        .day-name {
            flex-grow: 1;
        }
        .day-date {
            color: #666;
            margin-left: 4px;
            font-size: 6pt;
        }
        .day-tag {
            padding: 1px 4px;
            border-radius: 8px;
            font-size: 6pt;
            margin-left: 4px;
            letter-spacing: 0.2px;
        }
        .tag-today {
            background-color: #e8f5e9;
            color: #1b5e20;
        }
        .tag-past {
            background-color: #f1f3f4;
            color: #666;
        }
        .tag-upcoming {
            background-color: #e3f2fd;
            color: #0d47a1;
        }
        .tag-invalid {
            background-color: #f1f3f4;
            color: #666;
        }
        .ticket-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #f8f9fa;
            padding: 8px 10px;
            border-top: 1px solid #eee;
            font-size: 6pt;
            color: #666;
            text-align: center;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 2px 6px;
            background-color: #4CAF50;
            color: white;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 8px;
            letter-spacing: 0.5px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .ticket-decoration {
            position: absolute;
            left: -10px;
            top: -10px;
            width: 50px;
            height: 50px;
            background: #f0f4f8;
            border-radius: 50%;
            opacity: 0.5;
            z-index: 0;
        }
        .ticket-decoration-2 {
            position: absolute;
            right: -20px;
            bottom: 100px;
            width: 70px;
            height: 70px;
            background: #f0f4f8;
            border-radius: 50%;
            opacity: 0.4;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket">
            <!-- Decorative elements -->
            <div class="ticket-decoration"></div>
            <div class="ticket-decoration-2"></div>
            
            <!-- Status Badge -->
            <div class="status-badge">VALID</div>
            
            <!-- Ticket Header -->
            <div class="ticket-header">
                <h1 class="ticket-logo">ZURIW25 CONFERENCE</h1>
                <p class="ticket-subtitle">Official Attendee Ticket</p>
            </div>
            
            <!-- Ticket Body -->
            <div class="ticket-body">
                <!-- Ticket Number and QR -->
                <div class="ticket-number-wrapper">
                    <div class="ticket-number-section">
                        <div class="ticket-number-label">TICKET NUMBER</div>
                        <div class="ticket-number">{{ $ticket->ticket_number }}</div>
                    </div>
                </div>
                
                <!-- Participant Info -->
                <div class="section">
                    <div class="section-title">ATTENDEE</div>
                    <div class="section-content">
                        <div class="info-row">
                            <div class="info-label">Name:</div>
                            <div class="info-value">{{ $ticket->participant->full_name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Role:</div>
                            <div class="info-value">{{ $ticket->participant->role }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Category:</div>
                            <div class="info-value">
                                @php
                                    $categories = [
                                        'general' => 'General Participant',
                                        'invited' => 'Invited Guest',
                                        'internal' => 'Internal Participant',
                                        'coordinators' => 'Session Coordinator'
                                    ];
                                @endphp
                                {{ $categories[$ticket->participant->category] ?? $ticket->participant->category }}
                            </div>
                        </div>
                        @if($ticket->participant->organization)
                        <div class="info-row">
                            <div class="info-label">Organization:</div>
                            <div class="info-value">{{ $ticket->participant->organization }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Valid Days -->
                <div class="section days-section">
                    <div class="section-title">VALID DAYS</div>
                                        <div class="section-content">                        @forelse($conferenceDays as $day)                            <div class="day-row">                                @php                                    $isValid = false;                                    if ($day->id == 1 && $ticket->day1_valid) {                                        $isValid = true;                                    } elseif ($day->id == 2 && $ticket->day2_valid) {                                        $isValid = true;                                    } elseif ($day->id == 3 && $ticket->day3_valid) {                                        $isValid = true;                                    }                                @endphp                                                                @if($isValid)                                    <div class="day-indicator day-valid">✓</div>                                    <div class="day-name">{{ $day->name }}</div>                                    <div class="day-date">{{ $day->date->format('M j') }}</div>                                    @if($day->date->isToday())                                        <span class="day-tag tag-today">Today</span>                                    @elseif($day->date->isPast())                                        <span class="day-tag tag-past">Past</span>                                    @else                                        <span class="day-tag tag-upcoming">Upcoming</span>                                    @endif                                @else
                                    <div class="day-indicator day-invalid">✗</div>
                                    <div class="day-name" style="color: #999;">{{ $day->name }}</div>
                                    <div class="day-date" style="color: #999;">{{ $day->date->format('M j') }}</div>
                                    <span class="day-tag tag-invalid">Not Valid</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Payment Status -->
                <div class="section">
                    <div class="section-title">PAYMENT</div>
                    <div class="section-content">
                        <div class="info-row">
                            <div class="info-label">Status:</div>
                            <div class="info-value" style="
                                @if($ticket->participant->payment_status == 'Not Paid') color: #d32f2f;
                                @elseif($ticket->participant->payment_status == 'Waived' || $ticket->participant->payment_status == 'Not Applicable') color: #1565c0;
                                @elseif($ticket->participant->payment_status == 'Complimentary') color: #6a1b9a;
                                @else color: #2e7d32;
                                @endif
                            ">{{ $ticket->participant->payment_status }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Issued By:</div>
                            <div class="info-value">{{ $ticket->registeredBy->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Issue Date:</div>
                            <div class="info-value">{{ $ticket->created_at->format('M j, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Footer -->
            <div class="ticket-footer">
                Present this ticket for entry. Valid only for dates indicated. Not transferable.
            </div>
        </div>
    </div>
</body>
</html> 