<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ALS Form 1 - Masterlist of Enrolled Learners - {{ $year }}</title>
    <style>
        @page {
            margin: 18mm 12mm 18mm 12mm;
            size: a4 landscape;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #111827;
            line-height: 1.25;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h4 {
            margin: 0;
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: normal;
        }
        .header-title h3 {
            margin: 2px 0;
            font-size: 10.5pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #0038A8;
        }
        .header-title h2 {
            margin: 2px 0 4px 0;
            font-size: 11.5pt;
            font-weight: 800;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8pt;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .meta-table td {
            padding: 3px 6px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table thead {
            display: table-header-group;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .data-table th, .data-table td {
            border: 1px solid #374151;
            padding: 3px 4px;
            text-align: left;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            font-size: 7.5pt;
            text-transform: uppercase;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-mono { font-family: monospace; }
        
        /* Summary Grid */
        .summary-box {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }
        .summary-table th, .summary-table td {
            border: 1px solid #475569;
            padding: 3px 6px;
            text-align: center;
        }
        .summary-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
        }
        .summary-table td.label-col {
            text-align: left;
            font-weight: bold;
        }
        .summary-total-row {
            background-color: #e2e8f0;
            font-weight: bold;
        }

        /* Signatures block */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            page-break-inside: avoid;
        }
        .signatures-table td {
            padding: 4px 10px;
            vertical-align: top;
            width: 25%;
        }
        .sig-line {
            border-bottom: 1px solid #111827;
            margin-top: 25px;
            margin-bottom: 2px;
        }
        .sig-label {
            font-size: 7pt;
            color: #4b5563;
        }
        .sig-name {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .footer-note {
            font-size: 6.5pt;
            color: #64748b;
            text-align: right;
            margin-top: 6px;
            border-top: 1px solid #e2e8f0;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left; font-size: 8.5pt; font-weight: bold; color: #0038A8;">
                ALS FORM 1<br/>
                <span style="font-size: 7pt; color: #64748b; font-weight: normal;">(DepEd SDO San Juan)</span>
            </td>
            <td class="header-title" style="width: 70%;">
                <h4>Republic of the Philippines &bull; Department of Education</h4>
                <h3>National Capital Region &bull; Schools Division Office of San Juan City</h3>
                <h2>MASTERLIST OF ENROLLED LEARNERS (ALS FORM 1)</h2>
                <div style="font-size: 8pt; font-style: italic;">Alternative Learning System (ALS) &bull; Calendar Year {{ $year }}</div>
            </td>
            <td style="width: 15%; text-align: right; font-size: 7pt; color: #64748b;">
                Generated: {{ $generatedAt }}
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 30%;"><strong>Region:</strong> National Capital Region (NCR)</td>
            <td style="width: 35%;"><strong>Division:</strong> SDO San Juan City</td>
            <td style="width: 35%;"><strong>Calendar Year:</strong> CY {{ $year }}</td>
        </tr>
        <tr>
            <td><strong>Center (CLC):</strong> {{ $clc ? $clc->clc_name : 'All San Juan CLCs (Consolidated)' }}</td>
            <td><strong>Barangay / Location:</strong> {{ $clc ? 'Brgy. ' . $clc->barangay : 'Division Wide' }}</td>
            <td><strong>Assigned Mobile Teacher:</strong> {{ $clc && $clc->assignedTeacher ? $clc->assignedTeacher->full_name : 'Mobile Teacher Pool' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No.</th>
                <th style="width: 11%;">LRN</th>
                <th style="width: 18%;">Name of Learner<br/><span style="font-size: 6pt; text-transform: none;">(Last, First, Ext., Middle)</span></th>
                <th style="width: 4%;">Sex</th>
                <th style="width: 8%;">Birthdate<br/><span style="font-size: 6pt; text-transform: none;">(MM/DD/YYYY)</span></th>
                <th style="width: 4%;">Age</th>
                <th style="width: 18%;">Residential Address<br/><span style="font-size: 6pt; text-transform: none;">(House/Street, Barangay)</span></th>
                <th style="width: 10%;">Prior Attainment</th>
                <th style="width: 10%;">Program Track</th>
                <th style="width: 7%;">Mode</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $index => $enr)
                @php
                    $profile = $enr->user?->profile;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-mono">{{ $enr->user?->lrn ?: 'PENDING' }}</td>
                    <td>
                        <strong>{{ $profile ? $profile->last_name : $enr->user?->username }}</strong>, 
                        {{ $profile ? $profile->first_name : '' }} 
                        {{ $profile && $profile->name_extension ? $profile->name_extension : '' }}
                        {{ $profile && $profile->middle_name ? mb_substr($profile->middle_name, 0, 1) . '.' : '' }}
                    </td>
                    <td class="text-center">{{ $profile ? strtoupper(substr($profile->gender, 0, 1)) : '-' }}</td>
                    <td class="text-center">{{ $profile && $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('m/d/Y') : '-' }}</td>
                    <td class="text-center">{{ $profile ? $profile->age : '-' }}</td>
                    <td>{{ $profile ? $profile->street_address . ', Brgy. ' . $profile->barangay : 'San Juan City' }}</td>
                    <td>{{ $profile ? \Illuminate\Support\Str::limit($profile->highest_education_attained, 20) : '-' }}</td>
                    <td class="text-center"><strong>{{ $enr->program_track }}</strong></td>
                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $enr->delivery_mode)) }}</td>
                    <td class="text-center">{{ ucfirst($enr->enrollment_status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 16px; color: #64748b;">
                        No enrolled learners found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Official DepEd Form 1 Summary Breakdown Table -->
    @if(isset($summary))
        <div class="summary-box">
            <div style="font-weight: bold; font-size: 7.5pt; margin-bottom: 2px; text-transform: uppercase; color: #0038A8;">
                Statistical Summary Breakdown (DepEd ALS Form 1)
            </div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 28%;" class="text-left">Program Track Category</th>
                        <th colspan="3" style="width: 24%;">Enrolment by Sex</th>
                        <th colspan="4" style="width: 48%;">Status Distribution</th>
                    </tr>
                    <tr>
                        <th style="width: 8%;">Male</th>
                        <th style="width: 8%;">Female</th>
                        <th style="width: 8%;">Total</th>
                        <th style="width: 12%;">Enrolled</th>
                        <th style="width: 12%;">Under Review</th>
                        <th style="width: 12%;">Completed</th>
                        <th style="width: 12%;">Pending / Other</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="label-col">Basic Literacy Program (BLP)</td>
                        <td>{{ $summary['tracks']['BLP']['male'] ?? 0 }}</td>
                        <td>{{ $summary['tracks']['BLP']['female'] ?? 0 }}</td>
                        <td><strong>{{ $summary['tracks']['BLP']['total'] ?? 0 }}</strong></td>
                        <td>{{ $enrollments->where('program_track', 'BLP')->where('enrollment_status', 'enrolled')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'BLP')->where('enrollment_status', 'under_review')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'BLP')->where('enrollment_status', 'completed')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'BLP')->whereNotIn('enrollment_status', ['enrolled', 'under_review', 'completed'])->count() }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">A&amp;E Elementary</td>
                        <td>{{ $summary['tracks']['A&E_Elementary']['male'] ?? 0 }}</td>
                        <td>{{ $summary['tracks']['A&E_Elementary']['female'] ?? 0 }}</td>
                        <td><strong>{{ $summary['tracks']['A&E_Elementary']['total'] ?? 0 }}</strong></td>
                        <td>{{ $enrollments->where('program_track', 'A&E_Elementary')->where('enrollment_status', 'enrolled')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'A&E_Elementary')->where('enrollment_status', 'under_review')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'A&E_Elementary')->where('enrollment_status', 'completed')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'A&E_Elementary')->whereNotIn('enrollment_status', ['enrolled', 'under_review', 'completed'])->count() }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">A&amp;E Junior High School</td>
                        <td>{{ $summary['tracks']['A&E_Junior_High']['male'] ?? 0 }}</td>
                        <td>{{ $summary['tracks']['A&E_Junior_High']['female'] ?? 0 }}</td>
                        <td><strong>{{ $summary['tracks']['A&E_Junior_High']['total'] ?? 0 }}</strong></td>
                        <td>{{ $enrollments->where('program_track', 'A&E_Junior_High')->where('enrollment_status', 'enrolled')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'A&E_Junior_High')->where('enrollment_status', 'under_review')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'A&E_Junior_High')->where('enrollment_status', 'completed')->count() }}</td>
                        <td>{{ $enrollments->where('program_track', 'A&E_Junior_High')->whereNotIn('enrollment_status', ['enrolled', 'under_review', 'completed'])->count() }}</td>
                    </tr>
                    <tr class="summary-total-row">
                        <td class="label-col">GRAND TOTAL</td>
                        <td><strong>{{ $summary['male'] ?? 0 }}</strong></td>
                        <td><strong>{{ $summary['female'] ?? 0 }}</strong></td>
                        <td><strong>{{ $summary['total'] ?? 0 }}</strong></td>
                        <td><strong>{{ $summary['status_breakdown']['enrolled'] ?? 0 }}</strong></td>
                        <td><strong>{{ $summary['status_breakdown']['under_review'] ?? 0 }}</strong></td>
                        <td><strong>{{ $summary['status_breakdown']['completed'] ?? 0 }}</strong></td>
                        <td><strong>{{ ($summary['total'] ?? 0) - (($summary['status_breakdown']['enrolled'] ?? 0) + ($summary['status_breakdown']['under_review'] ?? 0) + ($summary['status_breakdown']['completed'] ?? 0)) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

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
                <div class="sig-label">Division ALS Focal Person / EPSA II</div>
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

    <div class="footer-note">
        DepEd SDO San Juan City &bull; Form 1 Masterlist &bull; RA 11510 Compliant &bull; Printed: {{ $generatedAt }}
    </div>
</body>
</html>

