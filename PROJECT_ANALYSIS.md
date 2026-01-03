# Procurement Management System - Project Analysis

## Overview
This is a **Government Procurement Management System** built for the **Department of Information and Communications Technology (DICT)**, Philippines. It's designed to digitize and streamline the entire procurement workflow from purchase requests to purchase orders, following Philippine government procurement regulations (RA 9184 - Government Procurement Reform Act).

## Purpose
The system automates and manages the complete procurement lifecycle for government agencies, ensuring compliance with procurement laws, proper documentation, and transparent processes.

## Target Users & Roles

### 1. **END_USER**
- Department staff who need to procure items/services
- Creates Purchase Requests (PRs)
- Tracks their PR status through the workflow
- Views their own procurement statistics

### 2. **PROCUREMENT_OFFICER**
- Reviews and validates Purchase Requests
- Creates RFQs (Request for Quotation)
- Manages the procurement process
- Oversees active RFQs

### 3. **CANVASSER**
- Receives canvassing tasks
- Contacts suppliers and collects quotations
- Manages supplier quotations
- Updates canvass status

### 4. **BAC_SECRETARIAT**
- Prepares BAC (Bids and Awards Committee) documents
- Manages document workflow
- Coordinates with BAC members

### 5. **BAC_CHAIR**
- Reviews and approves BAC documents
- Makes final procurement decisions
- Signs approval documents

### 6. **BAC_MEMBER**
- Reviews BAC documents
- Participates in approval process
- Provides recommendations

### 7. **SUPPLIER**
- Receives RFQs
- Submits quotations
- Manages purchase orders

### 8. **ADMIN**
- System administrator
- Full access to all features
- Manages users and system settings

## Complete Procurement Workflow

### Stage 1: Purchase Request (PR)
- **Status**: `PR_UNDER_REVIEW`
- End User creates PR with:
  - Project title and description
  - PR items (item code, name, quantity, unit of measure, estimated price)
  - Fund source
  - Estimated budget
  - Urgency level (LOW, MEDIUM, HIGH, URGENT)
  - Department information
- System validates required documents:
  - ✓ Signatures
  - ✓ Specifications
  - ✓ Quantities
  - ✓ Market Survey
- Procurement Officer reviews and validates PR

### Stage 2: RFQ (Request for Quotation)
- **Status**: `RFQ_READY` → `RFQ_DISSEMINATED`
- Procurement Officer creates RFQ from approved PR
- Includes:
  - Delivery schedule
  - Payment terms
  - Canvassing deadline
- RFQ is disseminated to suppliers/canvassers

### Stage 3: Canvassing
- **Status**: `CANVASS_COMPLETE`
- Canvasser receives task assignment
- Contacts multiple suppliers
- Collects supplier quotations with:
  - Quote price
  - Delivery days
  - Compliance status
  - Supporting documents
- System tracks:
  - Supplier information
  - Quotation details
  - Selection status

### Stage 4: BAC Documents Preparation
- **Status**: `BAC_DOCS_READY`
- BAC Secretariat prepares required documents:
  - **Abstract of Quotations** - Summary of all supplier quotes
  - **Price Matrix** - Comparative pricing analysis
  - **TWG Cert** - Technical Working Group Certificate
  - **Recommendation** - BAC recommendation document
  - **Resolution** - BAC resolution document
- Documents include procurement mode:
  - SHOPPING
  - SVP (Small Value Procurement)
  - PUBLIC_BIDDING
  - NEGOTIATED
  - DIRECT_CONTRACTING

### Stage 5: BAC Approval
- **Status**: `BAC_APPROVED`
- Multi-level approval routing:
  - BAC Members review
  - BAC Chair final approval
- System tracks:
  - Approval sequence
  - Time spent on each approval
  - Comments and feedback
  - Digital signatures

### Stage 6: Purchase Order Generation
- **Status**: `PO_APPROVED`
- System generates Purchase Order (PO) with:
  - PO number
  - Selected supplier details
  - Contract amount
  - Delivery instructions
  - Payment terms
  - Delivery deadline

### Stage 7: Supplier Conforme
- **Status**: `AWAITING_CONFORME`
- PO sent to supplier
- Supplier confirms acceptance (conforme)

### Stage 8: PO Completion
- **Status**: `PO_COMPLETE`
- Delivery received
- Payment processed

### Stage 9: COA Stamping
- **Status**: `COA_STAMPED`
- Commission on Audit (COA) stamps the document
- Final stage - procurement complete

## Key Features

### 1. **Document Management**
- Upload and store procurement documents
- Track signed documents
- Version control
- Document compliance checking

### 2. **Activity Logging**
- Complete audit trail
- User actions tracked
- Timestamped activities
- Compliance reporting

### 3. **Role-Based Dashboard**
- Customized views per role
- Relevant statistics
- Pending tasks
- Workflow visualization

### 4. **Status Tracking**
- Real-time status updates
- Workflow progression
- Status history
- Notifications (to be implemented)

### 5. **Approval Routing**
- Sequential approval workflow
- Multi-level approvals
- Time tracking
- Comments and feedback

### 6. **Supplier Management**
- Supplier database
- Quotation comparison
- Compliance tracking
- Selection management

## Database Structure

### Core Tables
1. **users** - All system users (end users, officers, BAC members, suppliers)
2. **purchase_requests** - Main PR records
3. **pr_items** - Individual items in each PR
4. **rfqs** - Request for Quotation records
5. **canvasses** - Canvassing tasks
6. **supplier_quotations** - Supplier quote submissions
7. **quotation_items** - Line items in quotations
8. **bac_documents** - BAC document records
9. **approval_routings** - Approval workflow tracking
10. **purchase_orders** - Generated purchase orders
11. **documents** - Uploaded documents
12. **signed_documents** - Digitally signed documents
13. **activity_logs** - System audit trail

## Technology Stack
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: SQLite (development)
- **Frontend**: Blade Templates + Tailwind CSS
- **Build Tool**: Vite
- **Authentication**: Laravel UI

## Current Development Status

### ✅ Completed
- Database schema and migrations
- User authentication system
- Role-based access control
- Models and relationships
- Dashboard with role-based views
- Basic routing structure

### 🚧 In Progress / To Be Implemented
- Purchase Request CRUD operations
- RFQ management interface
- Canvassing module
- BAC document generation
- Purchase Order generation
- Document upload/management
- Approval workflow automation
- Email notifications
- Reporting module
- Supplier portal

## Compliance & Regulations
This system is designed to comply with:
- **RA 9184** - Government Procurement Reform Act (Philippines)
- **COA Rules** - Commission on Audit regulations
- **BAC Guidelines** - Bids and Awards Committee procedures
- **Government Procurement Manual**

## Project Location
- **Organization**: DICT (Department of Information and Communications Technology)
- **Country**: Philippines
- **Purpose**: Government procurement digitization

## Next Steps for Development
1. Implement Purchase Request creation/editing interface
2. Build RFQ management module
3. Develop canvassing workflow
4. Create BAC document templates and generators
5. Implement approval routing system
6. Build Purchase Order generation
7. Add document upload/management
8. Implement notifications
9. Create reporting dashboards
10. Add supplier portal

