<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Ticket - {{ $ticket->ticket_number }}</title>
    <style>
        /* Page and document setup for small ticket size */
        @page {
            size: 450px 750px;
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 9pt;
            line-height: 1.3;
            background-color: #f5f5f5;
        }
        /* Ticket container */
        .ticket-container {
            width: 450px;
            height: 750px;
            overflow: hidden;
            position: relative;
            margin: 50px auto;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .ticket {
            width: 100%;
            height: 100%;
            border: 1px solid #ddd;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            background-color: white;
        }
        .ticket-header {
            background-color: #041E42;
            color: white;
            padding: 15px;
            position: relative;
        }
        .ticket-logo {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }
        .ticket-subtitle {
            font-size: 9pt;
            text-align: center;
            color: #ccc;
            margin: 4px 0 0 0;
        }
        .ticket-body {
            padding: 10px;
            position: relative;
        }
        .ticket-number-section {
            background-color: #f8f9fa;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #eaeaea;
            text-align: center;
        }
        .ticket-number-label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 4px;
        }
        .ticket-number {
            font-size: 14pt;
            font-weight: bold;
            color: #041E42;
            letter-spacing: 1px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #041E42;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 1px solid #eee;
        }
        .section-content {
            padding: 5px 0;
        }
        .info-row {
            margin-bottom: 5px;
            display: flex;
            font-size: 8pt;
        }
        .info-label {
            color: #666;
            width: 100px;
            flex-shrink: 0;
        }
        .info-value {
            font-weight: 500;
            flex-grow: 1;
        }
        .days-section {
            margin-bottom: 12px;
        }
        .day-row {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
            font-size: 8pt;
        }
        .day-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 6px;
            font-size: 7pt;
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
        }
        .day-tag {
            padding: 1px 5px;
            border-radius: 10px;
            font-size: 7pt;
            margin-left: 4px;
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
            padding: 8px 15px;
            border-top: 1px solid #eee;
            font-size: 7pt;
            color: #666;
        }
        .ticket-qr {
            position: absolute;
            bottom: 45px;
            right: 15px;
            width: 80px;
            height: 80px;
            background-color: #f1f3f4;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-text {
            font-size: 6pt;
            color: #666;
            text-align: center;
        }
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 3px 8px;
            background-color: #4CAF50;
            color: white;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 10px;
        }

        /* Print controls styling */
        .print-controls {
            width: 450px;
            margin: 20px auto;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            background-color: #041E42;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            margin: 0 8px;
            border: none;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #0A2E5C;
        }
        
        /* When printing, hide controls and ensure backgrounds print */
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            .print-controls {
                display: none;
            }
            .ticket-container {
                margin: 0 auto;
                box-shadow: none;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="btn" onclick="window.print()">Print Now</button>
        <a class="btn" href="{{ route('usher.registration.ticket', $ticket->id) }}">Return to Ticket</a>
    </div>
    
    <div class="ticket-container">
        <div class="ticket">
            <!-- Status Badge -->
            <div class="status-badge">VALID</div>
            
            <!-- Ticket Header -->
            <div class="ticket-header">
                <h1 class="ticket-logo">ZURIW25 CONFERENCE</h1>
                <p class="ticket-subtitle">Official Attendee Ticket</p>
            </div>
            
            <!-- Ticket Body -->
            <div class="ticket-body">
                <!-- Ticket Number -->
                <div class="ticket-number-section">
                    <div class="ticket-number-label">TICKET NUMBER</div>
                    <div class="ticket-number">{{ $ticket->ticket_number }}</div>
                </div>
                
                <!-- Participant Info -->
                <div class="section">
                    <div class="section-title">ATTENDEE INFORMATION</div>
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
                    <div class="section-title">VALID ATTENDANCE DAYS</div>
                    <div class="section-content">
                        @foreach($conferenceDays as $day)
                            <div class="day-row">
                                @if(($day->id == 1 && $ticket->day1_valid) || 
                                    ($day->id == 2 && $ticket->day2_valid) || 
                                    ($day->id == 3 && $ticket->day3_valid))
                                    <div class="day-indicator day-valid">✓</div>
                                    <div class="day-name">{{ $day->name }}</div>
                                    <div class="day-date">{{ $day->date->format('M j') }}</div>
                                    @if($day->date->isToday())
                                        <span class="day-tag tag-today">Today</span>
                                    @elseif($day->date->isPast())
                                        <span class="day-tag tag-past">Past</span>
                                    @else
                                        <span class="day-tag tag-upcoming">Upcoming</span>
                                    @endif
                                @else
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
                    <div class="section-title">PAYMENT DETAILS</div>
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
                
                <!-- QR Code Placeholder -->
                <div class="ticket-qr">
                    <div class="qr-text">
                        Scan for<br>verification<br>
                        <strong>{{ $ticket->ticket_number }}</strong>
                    </div>
                </div>
            </div>
            
            <!-- Ticket Footer -->
            <div class="ticket-footer">
                This ticket must be presented for entry. Valid only for dates indicated above. Not transferable.
            </div>
        </div>
    </div>
</body>
</html> 