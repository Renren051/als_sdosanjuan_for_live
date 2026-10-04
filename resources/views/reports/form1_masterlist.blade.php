<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ALS Form 1 - Masterlist of Enrolled Learners</title>
    <style>
        @page {
            margin: 25px 25px;
            size: a4 landscape;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #111827;
            line-height: 1.25;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h4 {
            margin: 0;
            font-size: 9pt;
            text-transform: uppercase;
            font-weight: normal;
        }
        .header-title h3 {
            margin: 2px 0;
            font-size: 11pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #0038A8;
        }
        .header-title h2 {
            margin: 2px 0 6px 0;
            font-size: 12pt;
            font-weight: 800;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 2px 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #374151;
            padding: 4px 5px;
            text-align: left;
        }
        .data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-mono { font-family: monospace; }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 7.5pt;
            border-radius: 3px;
            font-weight: bold;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .signatures-table td {
            padding: 6px 12px;
            vertical-align: top;
            width: 25%;
        }
        .sig-line {
            border-bottom: 1px solid #111827;
            margin-top: 30px;
            margin-bottom: 3px;
        }
        .sig-label {
            font-size: 7.5pt;
            color: #4b5563;
        }
        .sig-name {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left; font-size: 9pt; font-weight: bold; color: #0038A8;">
                ALS FORM 1<br/>
                <span style="font-size: 7.5pt; color: #6b7280; font-weight: normal;">(DepEd SDO San Juan)</span>
            </td>
            <td class="header-title" style="width: 70%;">
                <h4>Republic of the Philippines &bull; Department of Education</h4>
                <h3>National Capital Region &bull; Schools Division Office of San Juan City</h3>
                <h2>MASTERLIST OF ENROLLED LEARNERS (ALS FORM 1)</h2>
                <div style="font-size: 8.5pt; font-style: italic;">Alternative Learning System &bull; Calendar Year {{ $year }}</div>
            </td>
            <td style="width: 15%; text-align: right; font-size: 7.5pt; color: #4b5563;">
                Generated: {{ $generatedAt }}
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 30%;"><strong>Region:</strong> National Capital Region (NCR)</td>
            <td style="width: 35%;"><strong>Division:</strong> SDO San Juan City</td>
            <td style="width: 35%;"><strong>Calendar Year:</strong> {{ $year }}</td>
        </tr>
        <tr>
            <td><strong>Community Learning Center (CLC):</strong> {{ $clc ? $clc->clc_name : 'All San Juan CLCs' }}</td>
            <td><strong>Barangay / Location:</strong> {{ $clc ? 'Brgy. ' . $clc->barangay : 'San Juan City' }}</td>
            <td><strong>Assigned Mobile Teacher:</strong> {{ $clc && $clc->assignedTeacher ? $clc->assignedTeacher->full_name : 'Division Pool' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No.</th>
                <th style="width: 11%;">LRN</th>
                <th style="width: 18%;">Name of Learner<br/><span style="font-size: 6.5pt; text-transform: none;">(Last, First, Ext., Middle)</span></th>
                <th style="width: 4%;">Sex</th>
                <th style="width: 8%;">Birthdate<br/><span style="font-size: 6.5pt; text-transform: none;">(MM/DD/YYYY)</span></th>
                <th style="width: 4%;">Age</th>
                <th style="width: 18%;">Residential Address<br/><span style="font-size: 6.5pt; text-transform: none;">(House/Street, Barangay)</span></th>
                <th style="width: 10%;">Prior Education</th>
                <th style="width: 10%;">Program Track</th>
                <th style="width: 7%;">Mode</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $index => $enr)
                @php
                    $profile = $enr->user->profile;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-mono">{{ $enr->user->lrn ?: 'PENDING' }}</td>
                    <td>
                        <strong>{{ $profile ? $profile->last_name : $enr->user->username }}</strong>, 
                        {{ $profile ? $profile->first_name : '' }} 
                        {{ $profile && $profile->name_extension ? $profile->name_extension : '' }}
                        {{ $profile && $profile->middle_name ? mb_substr($profile->middle_name, 0, 1) . '.' : '' }}
                    </td>
                    <td class="text-center">{{ $profile ? strtoupper(substr($profile->gender, 0, 1)) : '-' }}</td>
                    <td class="text-center">{{ $profile && $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('m/d/Y') : '-' }}</td>
                    <td class="text-center">{{ $profile ? $profile->age : '-' }}</td>
                    <td>{{ $profile ? $profile->street_address . ', Brgy. ' . $profile->barangay : 'San Juan City' }}</td>
                    <td>{{ $profile ? Str::limit($profile->highest_education_attained, 20) : '-' }}</td>
                    <td class="text-center"><strong>{{ $enr->program_track }}</strong></td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $enr->delivery_mode)) }}</td>
                    <td class="text-center">{{ ucfirst($enr->enrollment_status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 18px; color: #6b7280;">
                        No enrolled learners found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-label">Prepared by:</div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $clc && $clc->assignedTeacher ? $clc->assignedTeacher->full_name : 'ALS Mobile Teacher' }}</div>
                <div class="sig-label">ALS Mobile Teacher / Facilitator</div>
            </td>
            <td>
                <div class="sig-label">Certified Correct:</div>
                <div class="sig-line"></div>
                <div class="sig-name">Dr. Maria Elena Santos</div>
                <div class="sig-label">Division ALS Focal Person / Coordinator</div>
            </td>
            <td>
                <div class="sig-label">Recommending Approval:</div>
                <div class="sig-line"></div>
                <div class="sig-name">Chief, CID</div>
                <div class="sig-label">Curriculum Implementation Division</div>
            </td>
            <td>
                <div class="sig-label">Approved:</div>
                <div class="sig-line"></div>
                <div class="sig-name">Schools Division Superintendent</div>
                <div class="sig-label">SDO San Juan City</div>
            </td>
        </tr>
    </table>
</body>
</html>
