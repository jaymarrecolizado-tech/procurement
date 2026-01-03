# System Testing Checklist

**Date:** 2025-01-XX  
**Agent:** Coach  
**Purpose:** Comprehensive testing checklist before production deployment

---

## ✅ Pre-Testing Setup

### 1. Environment Check
- [ ] Database migrations run successfully
- [ ] Storage link created (`php artisan storage:link`)
- [ ] Environment variables configured (.env)
- [ ] SMTP settings configured (for notifications)
- [ ] Application key generated
- [ ] Cache cleared (`php artisan cache:clear`, `php artisan config:clear`)

### 2. Dependencies Check
- [ ] Composer dependencies installed
- [ ] NPM dependencies installed (if using frontend assets)
- [ ] DomPDF installed and working
- [ ] All required PHP extensions enabled

---

## 🔐 Authentication & User Management

### 3. User Authentication
- [ ] User can register (creates END_USER role)
- [ ] User can login with email/password
- [ ] User can logout
- [ ] Remember me functionality works
- [ ] Rate limiting works (5 login attempts per minute)
- [ ] Password validation works
- [ ] Session management works

### 4. User Profile Management
- [ ] User can view own profile
- [ ] User can edit own profile (name, email, department)
- [ ] User can change own password
- [ ] Profile updates are logged

### 5. Admin User Management
- [ ] Admin can list all users
- [ ] Admin can create new users with role assignment
- [ ] Admin can edit user information (name, email, role, department)
- [ ] Admin can view user details with activity logs
- [ ] Admin can reset user passwords
- [ ] Admin can delete users (with self-deletion protection)
- [ ] Search and filter users works
- [ ] Role assignment works correctly

---

## 📋 Purchase Request (PR) Workflow

### 6. PR Creation & Management
- [ ] End User can create PR
- [ ] PR number auto-generates correctly
- [ ] Multiple items can be added to PR
- [ ] PR validation works (required fields)
- [ ] PR can be edited (before approval)
- [ ] PR can be viewed with all details
- [ ] PR can be deleted (with proper authorization)
- [ ] PR PDF generation works
- [ ] PR PDF preview works

### 7. PR Approval Routing
- [ ] Procurement Officer can assign approvers to PR
- [ ] Multiple approvers can be assigned with sequence
- [ ] Approvers receive notifications when PR is routed
- [ ] Approvers can view pending PRs in Approval Dashboard
- [ ] Sequential approval works (approver 1 must approve before approver 2)
- [ ] Approver can approve PR with comments
- [ ] Approver can reject PR with reason (minimum 10 characters)
- [ ] PR status updates to RFQ_READY when all approvals complete
- [ ] PR status reverts to PR_UNDER_REVIEW when rejected
- [ ] Approval history is tracked
- [ ] Time tracking works for approvals

---

## 📨 RFQ Workflow

### 8. RFQ Creation & Management
- [ ] Procurement Officer can create RFQ from approved PR
- [ ] RFQ number auto-generates correctly
- [ ] RFQ can be edited
- [ ] RFQ can be viewed with all details
- [ ] RFQ status can be updated manually
- [ ] RFQ links to related PR correctly

### 9. RFQ Approval Routing
- [ ] Procurement Officer can assign approvers to RFQ
- [ ] Multiple approvers can be assigned with sequence
- [ ] Approvers receive notifications when RFQ is routed
- [ ] Approvers can view pending RFQs in Approval Dashboard
- [ ] Sequential approval works
- [ ] Approver can approve RFQ with comments
- [ ] Approver can reject RFQ with reason
- [ ] RFQ status updates to ACTIVE when all approvals complete
- [ ] PR status updates to RFQ_DISSEMINATED when RFQ approved
- [ ] Approval history is tracked

---

## 🛒 Canvassing System

### 10. Canvass Management
- [ ] Canvasser can create canvass tasks
- [ ] Canvass can be assigned to canvassers
- [ ] Canvass can be edited
- [ ] Canvass can be viewed with all details
- [ ] Canvass status can be updated
- [ ] Overdue canvasses are tracked

### 11. Supplier Quotations
- [ ] Canvasser can create supplier quotations
- [ ] Multiple quotation items can be added
- [ ] Quotation can be edited
- [ ] Quotation can be viewed
- [ ] Winning quotation can be selected
- [ ] Quotation can be deleted
- [ ] Quotation items link to PR items correctly

---

## 📄 BAC Documents

### 12. BAC Document Creation
- [ ] BAC Secretariat can create BAC documents
- [ ] Abstract of Quotations auto-generates from supplier quotations
- [ ] Price Matrix auto-generates comparing all quotations
- [ ] Manual documents (TWG Cert, Recommendation, Resolution) can be created
- [ ] BAC document can be edited (before approval)
- [ ] BAC document can be viewed with formatted display
- [ ] BAC document can be deleted (before approval)
- [ ] PR status updates to BAC_DOCS_READY when first BAC doc created

### 13. BAC Document Approval Routing
- [ ] BAC Secretariat can assign approvers to BAC documents
- [ ] Multiple approvers can be assigned with sequence
- [ ] Approvers receive notifications when BAC doc is routed
- [ ] Approvers can view pending BAC docs in Approval Dashboard
- [ ] Sequential approval works
- [ ] Approver can approve BAC doc with comments
- [ ] Approver can reject BAC doc with reason
- [ ] BAC doc status updates to APPROVED when all approvals complete
- [ ] PR status updates to BAC_APPROVED when BAC doc approved
- [ ] Approval history is tracked

### 14. BAC Document PDF Generation
- [ ] ⚠️ **MISSING** - PDF generation for BAC documents
- [ ] ⚠️ **MISSING** - PDF download button
- [ ] ⚠️ **MISSING** - PDF preview button

---

## 🛍️ Purchase Orders

### 15. PO Creation & Management
- [ ] Procurement Officer can create PO from BAC-approved PR
- [ ] PO auto-populates from selected supplier quotation
- [ ] PO number auto-generates correctly
- [ ] PO can be edited (before dissemination)
- [ ] PO can be viewed with all details
- [ ] PO status can be updated manually
- [ ] PO PDF generation works
- [ ] PO PDF preview works
- [ ] PR status updates to PO_APPROVED when PO created
- [ ] PR status updates to AWAITING_CONFORME when PO disseminated

### 16. PO Completion & COA Stamping
- [ ] ⚠️ **PARTIAL** - Status can be updated via dropdown
- [ ] ⚠️ **MISSING** - Structured completion form
- [ ] ⚠️ **MISSING** - COA stamping form with reference/date
- [ ] ⚠️ **MISSING** - Workflow buttons for completion stages

---

## 📊 Approval Dashboard

### 17. Unified Approval View
- [ ] Users can access Approval Dashboard
- [ ] Dashboard shows pending approvals for PR, RFQ, BAC
- [ ] Filter by document type works (All, PR, RFQ, BAC)
- [ ] Statistics cards show correct counts
- [ ] "Ready to Approve" vs "Waiting for Others" status works
- [ ] Quick links to review documents work
- [ ] Approval history section shows recent approvals
- [ ] Time since pending displays correctly

---

## 🔔 Notifications System

### 18. Email Notifications
- [ ] Email sent when document is routed to approver
- [ ] Email sent when document is approved
- [ ] Email sent when document is rejected
- [ ] Email sent to next approver when previous approves
- [ ] Email templates are formatted correctly
- [ ] SMTP configuration works

### 19. In-App Notifications
- [ ] Notification bell shows unread count
- [ ] Notification dropdown shows recent notifications
- [ ] Notifications can be marked as read individually
- [ ] All notifications can be marked as read
- [ ] Clicking notification navigates to related document
- [ ] Unread count updates in real-time

---

## 📁 Document Management

### 20. Document Upload
- [ ] Documents can be uploaded for PR
- [ ] Documents can be uploaded for RFQ
- [ ] Documents can be uploaded for PO
- [ ] File size limit (10MB) is enforced
- [ ] Supported file types are validated (PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG)
- [ ] Files are stored in correct location
- [ ] Document metadata is saved correctly

### 21. Document Management
- [ ] Documents are listed on PR/RFQ/PO show pages
- [ ] Document preview works
- [ ] Document download works
- [ ] Document can be deleted (with authorization)
- [ ] Document type is displayed correctly
- [ ] File size is displayed in human-readable format
- [ ] Uploader and date are displayed

---

## 📈 Reporting & Analytics

### 22. Reports Dashboard
- [ ] Reports page is accessible
- [ ] Overall statistics display correctly
- [ ] Status distribution charts work
- [ ] Approval time statistics calculate correctly
- [ ] Top departments list works
- [ ] Top suppliers list works
- [ ] Monthly trends display correctly
- [ ] Top approvers list works
- [ ] Date range filtering works

---

## 🏪 Supplier Repository

### 23. Supplier Management
- [ ] Supplier repository is accessible
- [ ] Canvasser can create supplier entries
- [ ] Supplier name normalization works
- [ ] Duplicate detection works
- [ ] Supplier can be edited
- [ ] Supplier can be viewed with details
- [ ] Supplier can be deleted

### 24. Quotation History
- [ ] Quotation history can be created
- [ ] Bulk item entry works
- [ ] Item codes auto-generate
- [ ] Quotation age calculates correctly
- [ ] Images can be uploaded (single and multiple)
- [ ] Images can be previewed and downloaded
- [ ] Wildcard search works
- [ ] Filter by supplier/category/date works
- [ ] Activity logs are tracked

---

## 🔍 Search & Navigation

### 25. Search Functionality
- [ ] PR search works (by number, title, description)
- [ ] RFQ search works (by number, PR number, title)
- [ ] PO search works (by number, PR number, title)
- [ ] BAC documents search works
- [ ] User search works
- [ ] Supplier repository search works

### 26. Navigation
- [ ] All navigation links work
- [ ] Role-based navigation displays correctly
- [ ] Dashboard link works
- [ ] Reports link works
- [ ] Approval Dashboard link works
- [ ] User Management link works (admin only)
- [ ] Supplier Repository link works

---

## 🔒 Security & Authorization

### 27. Role-Based Access Control
- [ ] END_USER can only access own PRs
- [ ] PROCUREMENT_OFFICER can access all PRs/RFQs/POs
- [ ] BAC_SECRETARIAT can create BAC documents
- [ ] BAC_MEMBER/BAC_CHAIR can approve BAC documents
- [ ] CANVASSER can access canvassing features
- [ ] ADMIN has full access
- [ ] Unauthorized access returns 403

### 28. Data Validation
- [ ] Required fields are validated
- [ ] Email format is validated
- [ ] Numeric fields accept only numbers
- [ ] Date fields accept only valid dates
- [ ] File uploads are validated
- [ ] Password requirements are enforced

---

## 📝 Activity Logging

### 29. Audit Trail
- [ ] All CRUD operations are logged
- [ ] Approval actions are logged
- [ ] Status changes are logged
- [ ] User management actions are logged
- [ ] Activity logs contain correct information (user, action, timestamp, details)

---

## ⚠️ Known Missing Features (Non-Critical)

### 30. Missing but Not Blocking
- [ ] BAC Documents PDF Generation (high priority but not blocking)
- [ ] Supplier Conforme Tracking (high priority but not blocking)
- [ ] PO Completion & COA Stamping structured workflow (medium priority)
- [ ] Activity Log Viewing UI (medium priority)
- [ ] Advanced Search & Filtering (low priority)

---

## 🚀 Production Readiness Checklist

### 31. Final Checks
- [ ] All critical features tested and working
- [ ] No critical errors in logs
- [ ] Database backups configured
- [ ] Error handling is in place
- [ ] Performance is acceptable
- [ ] Security measures are in place
- [ ] Documentation is updated
- [ ] User training materials prepared

---

## 📊 Test Results Summary

**Total Test Items:** 31 categories, ~150+ individual tests

**Critical Features Status:**
- ✅ Authentication & User Management
- ✅ PR Workflow & Routing
- ✅ RFQ Workflow & Routing
- ✅ Canvassing System
- ✅ BAC Documents & Routing
- ✅ Purchase Orders
- ✅ Approval Dashboard
- ✅ Notifications System
- ✅ Document Management
- ✅ Reporting & Analytics
- ✅ Supplier Repository

**Missing Features (Non-Blocking):**
- ⚠️ BAC Documents PDF Generation
- ⚠️ Supplier Conforme Tracking
- ⚠️ PO Completion structured workflow

---

## 🎯 Testing Priority

### Must Test Before Production:
1. Authentication & User Management
2. PR/RFQ/BAC Routing & Approval
3. Approval Dashboard
4. Notifications
5. Document Upload/Download
6. Basic CRUD operations

### Should Test:
7. Reporting & Analytics
8. Supplier Repository
9. PDF Generation (PR/PO)
10. Search & Filtering

### Nice to Have:
11. Advanced features
12. Edge cases
13. Performance testing

---

## 📝 Notes

- Test with different user roles
- Test with sample data
- Test error scenarios
- Test edge cases
- Document any issues found
- Verify all routes work
- Check database integrity
- Verify file storage works

