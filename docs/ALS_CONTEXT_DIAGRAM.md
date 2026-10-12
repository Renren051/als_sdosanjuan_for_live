# ALS SDO San Juan City Context Diagram

![ALS SDO San Juan City system context diagram](ALS_CONTEXT_DIAGRAM.svg)

The system boundary groups the web SIS and administration portal, Laravel REST API, Android learning app, shared MySQL database, and file storage. Learners may use the web portal or Android app; Android reading and game activity can sync when connectivity is available. The ALSJay assistant uses Google Gemini only when configured. DepEd ALS Form 1 and Form 2 reports are generated for staff to export as files.

## Editable diagram source

```mermaid
flowchart LR
    learner[Applicant / Learner<br/>Web portal or Android app]
    teacher[Mobile Teacher<br/>CLC roster and learner review]
    officials[DepEd / SDO Officials<br/>Report recipient]

    system((ALS SDO San Juan City<br/>Student Information &amp; Learning System<br/><br/>Web SIS • LMS • Backend API<br/>Shared MySQL and file storage))

    admin[Division Administrator]
    superadmin[Super Administrator]
    gemini[Google Gemini API<br/>Optional assistant service]

    learner -->|ALS Form 2, documents, sign-in,<br/>offline progress and game-score sync| system
    system -->|Application status, modules,<br/>learning feedback| learner

    teacher -->|Enrollment and document review,<br/>CLC updates| system
    system -->|Assigned learner list, progress,<br/>game scores| teacher

    system -.->|ALS Form 1 and Form 2 reports<br/>manual file export| officials

    admin -->|Enrollment decisions, module publishing,<br/>CLC and banner settings| system
    system -->|Intake queue, dashboard,<br/>report exports| admin

    superadmin -->|Staff accounts, teacher / CLC<br/>assignments, access resets| system
    system -->|Account and assignment status| superadmin

    system -.->|Assistant question<br/>and conversation context| gemini
    gemini -.->|ALSJay response and<br/>grounded sources| system
```
