<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ALS Form 2 - Learner Enrolment Form - {{ $learner->lrn ?: $learner->username }}</title>
    <style>
        @page {
            margin: 20px 25px;
            size: a4 portrait;
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
            margin-bottom: 8px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h4 {
            margin: 0;
            font-size: 8.5pt;
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
            margin: 2px 0;
            font-size: 11.5pt;
            font-weight: 800;
        }
        .photo-box {
            width: 85px;
            height: 85px;
            border: 1px dashed #6b7280;
            text-align: center;
            vertical-align: middle;
            font-size: 7pt;
            color: #9ca3af;
            background-color: #f9fafb;
        }
        .section-header {
            background-color: #0038A8;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 3px 6px;
            margin-top: 8px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .info-table td {
            border: 1px solid #d1d5db;
            padding: 3px 5px;
            vertical-align: middle;
        }
        .label {
            font-size: 7pt;
            color: #4b5563;
            text-transform: uppercase;
            display: block;
            margin-bottom: 1px;
        }
        .val {
            font-size: 8.5pt;
            font-weight: bold;
            color: #111827;
        }
        .check-box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #111827;
            text-align: center;
            line-height: 9px;
            font-size: 8pt;
            margin-right: 3px;
        }
        .signature-block {
            margin-top: 15px;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signature-block td {
            width: 50%;
            padding: 10px 20px;
            vertical-align: top;
        }
        .sig-line {
            border-bottom: 1px solid #111827;
            margin-top: 35px;
            margin-bottom: 2px;
        }
        .footer-note {
            font-size: 7pt;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
            margin-top: 10px;
            page-break-inside: avoid;
        }
        .doc-check {
            display: inline-block;
            font-family: DejaVu Sans, sans-serif;
            font-weight: bold;
            color: #0038A8;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 15%; vertical-align: top;">
                <div style="font-weight: bold; font-size: 8pt; color: #0038A8;">ANNEX 2<br/>ALS FORM 2</div>
                <div style="font-size: 6.5pt; color: #6b7280;">(Revised 2019)</div>
            </td>
            <td class="header-title" style="width: 70%;">
                <h4>Republic of the Philippines &bull; Department of Education</h4>
                <h3>Schools Division Office &bull; San Juan City</h3>
                <h2>LEARNER ENROLMENT FORM (ALS FORM 2)</h2>
                <div style="font-size: 8pt; font-style: italic;">Alternative Learning System (ALS)</div>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: top;">
                <div class="photo-box">
                    <br/><br/>2x2 ID Photo<br/>Here
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table" style="background-color: #f8fafc;">
        <tr>
            <td style="width: 35%;">
                <span class="label">Learner Reference Number (LRN)</span>
                <span class="val" style="letter-spacing: 2px;">{{ $learner->lrn ?: 'NOT YET ASSIGNED' }}</span>
            </td>
            <td style="width: 35%;">
                <span class="label">Division / City</span>
                <span class="val">SDO San Juan City</span>
            </td>
            <td style="width: 30%;">
                <span class="label">Calendar Year</span>
                <span class="val">{{ $enrollment ? $enrollment->calendar_year : '2026' }}</span>
            </td>
        </tr>
    </table>

    <div class="section-header">Part I: Learner's Personal Information</div>
    <table class="info-table">
        <tr>
            <td style="width: 25%;">
                <span class="label">Last Name</span>
                <span class="val">{{ $profile ? $profile->last_name : '-' }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">First Name</span>
                <span class="val">{{ $profile ? $profile->first_name : '-' }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Middle Name</span>
                <span class="val">{{ $profile ? $profile->middle_name : '-' }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Name Extension</span>
                <span class="val">{{ $profile ? $profile->name_extension : 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Birthdate</span>
                <span class="val">{{ $profile && $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('F d, Y') : '-' }}</span>
            </td>
            <td>
                <span class="label">Age</span>
                <span class="val">{{ $profile ? $profile->age : '-' }} years old</span>
            </td>
            <td>
                <span class="label">Sex</span>
                <span class="val">{{ $profile ? ucfirst($profile->gender) : '-' }}</span>
            </td>
            <td>
                <span class="label">Civil Status</span>
                <span class="val">{{ $profile ? ucfirst($profile->civil_status) : '-' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label">Mother Tongue</span>
                <span class="val">{{ $profile ? $profile->mother_tongue : 'Tagalog / Filipino' }}</span>
            </td>
            <td>
                <span class="label">Contact Number</span>
                <span class="val">{{ $profile ? $profile->contact_number : '-' }}</span>
            </td>
            <td>
                <span class="label">Facebook / Social Account</span>
                <span class="val">{{ $profile ? $profile->facebook_name : 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <span class="label">Complete Residential Address</span>
                <span class="val">{{ $profile ? $profile->street_address . ', Brgy. ' . $profile->barangay . ', ' . $profile->city . ', ' . $profile->province : 'San Juan City' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">4Ps Beneficiary?</span>
                <span class="val">{{ $profile && $profile->is_4ps_beneficiary ? 'YES' : 'NO' }}</span>
            </td>
            <td>
                <span class="label">Indigenous People (IP)?</span>
                <span class="val">{{ $profile && $profile->is_ip ? 'YES (' . $profile->ip_community_name . ')' : 'NO' }}</span>
            </td>
            <td colspan="2">
                <span class="label">Person with Disability (PWD)?</span>
                <span class="val">{{ $profile && $profile->is_pwd ? 'YES (' . $profile->pwd_disability_type . ')' : 'NO' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label">Parent / Guardian Name</span>
                <span class="val">{{ $profile ? ($profile->guardian_name ?: ($profile->mother_name ?: $profile->father_name)) : 'N/A' }}</span>
            </td>
            <td colspan="2">
                <span class="label">Emergency Contact & Relationship</span>
                <span class="val">{{ $profile ? $profile->emergency_contact_name . ' (' . ($profile->emergency_contact_number ?: $profile->contact_number) . ')' : 'N/A' }}</span>
            </td>
        </tr>
    </table>

    <div class="section-header">Part II: Educational Background</div>
    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <span class="label">Highest Educational Attainment</span>
                <span class="val">{{ $profile ? $profile->highest_education_attained : '-' }}</span>
            </td>
            <td style="width: 50%;">
                <span class="label">Prior Formal School Attended</span>
                <span class="val">San Juan Elementary / High School (DepEd NCR)</span>
            </td>
        </tr>
    </table>

    <div class="section-header">Part III: Learning Program & Center Allocation</div>
    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <span class="label">Program Track Applied</span>
                <span class="val" style="font-size: 9.5pt; color: #0038A8;">
                    {{ $enrollment ? $enrollment->track_label : 'A&E Junior High School' }}
                </span>
            </td>
            <td style="width: 50%;">
                <span class="label">Community Learning Center (CLC)</span>
                <span class="val">{{ $enrollment && $enrollment->clc ? $enrollment->clc->clc_name : 'To be assigned' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Preferred Delivery Mode</span>
                <span class="val">{{ $enrollment ? ucfirst(str_replace('_', ' ', $enrollment->delivery_mode)) : 'Modular (Offline Mobile)' }}</span>
            </td>
            <td>
                <span class="label">Assigned Mobile Teacher</span>
                <span class="val">{{ $enrollment && $enrollment->assignedTeacher ? $enrollment->assignedTeacher->full_name : 'ALS Mobile Teacher Pool' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label">Enrollment Status & Assessment Remarks</span>
                <span class="val">{{ $enrollment ? ucfirst($enrollment->enrollment_status) . ' - ' . ($enrollment->remarks ?: 'Application submitted via ALS San Juan Portal') : 'Pending verification' }}</span>
            </td>
        </tr>
    </table>

    @php
        $submittedDocTypes = isset($documents) ? $documents->pluck('document_type')->toArray() : [];
        $hasPsa = in_array(\App\Models\LearnerDocument::TYPE_PSA_BIRTH_CERT, $submittedDocTypes);
        $hasBrgy = in_array(\App\Models\LearnerDocument::TYPE_BARANGAY_CLEARANCE, $submittedDocTypes);
        $hasSf10 = in_array(\App\Models\LearnerDocument::TYPE_FORM_137_SF10, $submittedDocTypes);
        $hasPhoto = in_array(\App\Models\LearnerDocument::TYPE_PHOTO_ID, $submittedDocTypes);
    @endphp
    <div class="section-header">Part IV: Submitted Document Requirements & Verification Status</div>
    <table class="info-table">
        <tr>
            <td style="width: 25%;">
                <span class="label">PSA Birth Certificate</span>
                <span class="val">
                    <span class="check-box">{!! $hasPsa ? '&#10003;' : '&nbsp;' !!}</span>
                    {{ $hasPsa ? 'Submitted' : 'Pending' }}
                </span>
            </td>
            <td style="width: 25%;">
                <span class="label">Barangay Clearance</span>
                <span class="val">
                    <span class="check-box">{!! $hasBrgy ? '&#10003;' : '&nbsp;' !!}</span>
                    {{ $hasBrgy ? 'Submitted' : 'Pending' }}
                </span>
            </td>
            <td style="width: 25%;">
                <span class="label">Form 137 / SF10</span>
                <span class="val">
                    <span class="check-box">{!! $hasSf10 ? '&#10003;' : '&nbsp;' !!}</span>
                    {{ $hasSf10 ? 'Submitted' : 'Pending' }}
                </span>
            </td>
            <td style="width: 25%;">
                <span class="label">2x2 ID Photo</span>
                <span class="val">
                    <span class="check-box">{!! $hasPhoto ? '&#10003;' : '&nbsp;' !!}</span>
                    {{ $hasPhoto ? 'Submitted' : 'Pending' }}
                </span>
            </td>
        </tr>
    </table>

    <table class="signature-block">
        <tr>
            <td>
                <p style="font-size: 7.5pt; text-align: justify; margin-bottom: 20px;">
                    I hereby certify that all information supplied in this ALS Form 2 enrolment questionnaire is true, correct, and complete to the best of my knowledge.
                </p>
                <div class="sig-line"></div>
                <div style="text-align: center; font-weight: bold; text-transform: uppercase;">
                    {{ $profile ? $profile->full_name : $learner->username }}
                </div>
                <div style="text-align: center; font-size: 7.5pt; color: #4b5563;">
                    Signature over Printed Name of Learner / Parent
                </div>
            </td>
            <td>
                <p style="font-size: 7.5pt; text-align: justify; margin-bottom: 20px;">
                    Intake application validated and verified against submitted PSA / Barangay credentials. Assigned to Community Learning Center.
                </p>
                <div class="sig-line"></div>
                <div style="text-align: center; font-weight: bold; text-transform: uppercase;">
                    {{ $enrollment && $enrollment->assignedTeacher ? $enrollment->assignedTeacher->full_name : 'ALS Mobile Teacher' }}
                </div>
                <div style="text-align: center; font-size: 7.5pt; color: #4b5563;">
                    ALS Mobile Teacher / Enrolment Facilitator
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        SDO San Juan City &bull; Alternative Learning System (ALS) Enrolment &bull; Form 2 Official Archive Copy &bull; Date Printed: {{ $generatedAt }}
    </div>
</body>
</html>
