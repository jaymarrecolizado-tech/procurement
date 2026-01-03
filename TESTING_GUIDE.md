# Testing Guide - Procurement Management System

## 🚀 Server Status

The Laravel development server should be running at: **http://localhost:8000**

## ✅ Pre-Testing Checklist

- [x] Database connected (MySQL - db_procurement)
- [x] Migrations run (all tables created)
- [x] Users seeded (6 test users available)
- [x] Server started on port 8000
- [x] All routes registered

## 🧪 Test Scenarios

### Test 1: Access Homepage
**URL**: http://localhost:8000/

**Expected Result**: 
- If not logged in: Redirects to `/login`
- If logged in: Redirects to `/dashboard`

**Steps**:
1. Open browser
2. Navigate to http://localhost:8000/
3. Should see login page

---

### Test 2: Login Functionality
**URL**: http://localhost:8000/login

**Test Credentials**:
```
Email: admin@dict.gov.ph
Password: password
```

**Expected Result**:
- Login form displays correctly
- After successful login, redirects to dashboard
- Session is created
- User information is displayed

**Steps**:
1. Navigate to http://localhost:8000/login
2. Enter email: `admin@dict.gov.ph`
3. Enter password: `password`
4. Click "Log in"
5. Should redirect to dashboard
6. Check if user name appears in header

**Additional Test Users**:
- Procurement Officer: `procurement@dict.gov.ph` / `password`
- End User: `enduser@dict.gov.ph` / `password`
- Canvasser: `canvasser@dict.gov.ph` / `password`
- BAC Chair: `bac.chair@dict.gov.ph` / `password`
- BAC Secretariat: `bac.secretariat@dict.gov.ph` / `password`

---

### Test 3: Registration
**URL**: http://localhost:8000/register

**Expected Result**:
- Registration form displays
- Can select role from dropdown
- After registration, auto-login and redirect to dashboard
- New user is created in database

**Steps**:
1. Navigate to http://localhost:8000/register
2. Fill in the form:
   - Name: Test User
   - Email: test@example.com
   - Role: Select "End User"
   - Department: Test Department (optional)
   - Password: password123
   - Confirm Password: password123
3. Click "Register"
4. Should auto-login and redirect to dashboard

**Test Invalid Registration**:
- Try registering with existing email (should show error)
- Try registering with mismatched passwords (should show error)
- Try registering with weak password (should show error)

---

### Test 4: Dashboard Access
**URL**: http://localhost:8000/dashboard

**Expected Result**:
- Dashboard displays with role-based statistics
- Welcome message shows user name and role
- Statistics cards display correctly
- Workflow visualization shows
- Recent Purchase Requests table displays
- Pending tasks section shows (if applicable)

**Steps**:
1. Login as any user
2. Navigate to http://localhost:8000/dashboard
3. Verify:
   - Welcome message with user name
   - Role displayed correctly
   - Statistics cards (should show numbers)
   - Workflow steps visualization
   - Recent PRs table (if any exist)

**Test Different Roles**:
- Login as different roles and verify dashboard shows different statistics:
  - **END_USER**: Shows their own PRs
  - **PROCUREMENT_OFFICER**: Shows all PRs and RFQs
  - **CANVASSER**: Shows canvass tasks
  - **BAC roles**: Shows BAC-related statistics

---

### Test 5: Profile Management
**URL**: http://localhost:8000/profile

**Expected Result**:
- Profile page displays current user information
- Can update name, email, department
- Can change password
- Success messages display after updates

**Steps**:
1. Login
2. Navigate to http://localhost:8000/profile
3. Update Profile:
   - Change name
   - Change email (to unique email)
   - Change department
   - Click "Update Profile"
   - Should see success message
4. Change Password:
   - Enter current password
   - Enter new password
   - Confirm new password
   - Click "Change Password"
   - Should see success message
   - Logout and login with new password to verify

---

### Test 6: Logout
**URL**: http://localhost:8000/logout (POST)

**Expected Result**:
- User is logged out
- Session is destroyed
- Redirects to login page
- Cannot access protected routes after logout

**Steps**:
1. While logged in, click logout (or navigate to logout route)
2. Should redirect to login page
3. Try accessing /dashboard directly
4. Should redirect back to login

---

### Test 7: Protected Routes
**Test**: Try accessing protected routes without login

**Expected Result**:
- All protected routes redirect to login
- Cannot access dashboard, profile, etc. without authentication

**Steps**:
1. Logout (or use incognito/private window)
2. Try accessing:
   - http://localhost:8000/dashboard
   - http://localhost:8000/profile
3. All should redirect to login

---

### Test 8: Remember Me Functionality
**URL**: http://localhost:8000/login

**Expected Result**:
- "Remember me" checkbox works
- Session persists after browser close (if checked)

**Steps**:
1. Go to login page
2. Check "Remember me" checkbox
3. Login
4. Close browser
5. Reopen browser and go to site
6. Should still be logged in

---

## 🐛 Common Issues & Solutions

### Issue: "404 Not Found" on routes
**Solution**: 
```bash
php artisan route:clear
php artisan config:clear
```

### Issue: "Class not found" errors
**Solution**: 
- Missing models are expected (RFQ, Canvass, etc.)
- Dashboard handles this gracefully
- Create models when ready

### Issue: "Database connection error"
**Solution**: 
- Check `.env` file has correct database credentials
- Ensure MySQL is running
- Run `php artisan migrate` if needed

### Issue: "No users found"
**Solution**: 
```bash
php artisan db:seed
```

### Issue: "Session driver not working"
**Solution**: 
- Check `storage/framework/sessions` is writable
- Check `.env` has `SESSION_DRIVER=file`

### Issue: "CSRF token mismatch"
**Solution**: 
- Clear browser cache
- Ensure cookies are enabled
- Check `APP_KEY` is set in `.env`

## 📊 Expected Dashboard Statistics

### For END_USER:
- Total PRs: Number of PRs created by user
- Pending Review: PRs with status PR_UNDER_REVIEW
- In Progress: PRs in various active statuses
- Completed: PRs with status PO_COMPLETE or COA_STAMPED

### For PROCUREMENT_OFFICER:
- Total PRs: All PRs in system
- Pending Review: PRs awaiting review
- Active RFQs: RFQs with ACTIVE status (may show 0 if models not created)
- Completed: Completed purchase orders

### For CANVASSER:
- Total Tasks: Canvass tasks assigned
- Pending Tasks: Tasks with PENDING status
- In Progress: Tasks with IN_PROGRESS status
- Completed: Tasks with COMPLETED status

## ✅ Success Criteria

All tests pass if:
- [x] Can login with test credentials
- [x] Can register new users
- [x] Dashboard displays correctly
- [x] Profile can be updated
- [x] Password can be changed
- [x] Logout works
- [x] Protected routes require authentication
- [x] No fatal errors or crashes
- [x] Statistics display (even if 0)

## 🎯 Next Steps After Testing

1. **If all tests pass**: 
   - Create missing model classes (RFQ, Canvass, etc.)
   - Implement Purchase Request CRUD
   - Build RFQ management

2. **If issues found**:
   - Check error logs: `storage/logs/laravel.log`
   - Review browser console for JavaScript errors
   - Check server logs for PHP errors

## 📝 Test Results Template

```
Test Date: ___________
Tester: ___________

Test 1 - Homepage: [ ] Pass [ ] Fail
Test 2 - Login: [ ] Pass [ ] Fail
Test 3 - Registration: [ ] Pass [ ] Fail
Test 4 - Dashboard: [ ] Pass [ ] Fail
Test 5 - Profile: [ ] Pass [ ] Fail
Test 6 - Logout: [ ] Pass [ ] Fail
Test 7 - Protected Routes: [ ] Pass [ ] Fail
Test 8 - Remember Me: [ ] Pass [ ] Fail

Issues Found:
_________________________________
_________________________________
_________________________________

Notes:
_________________________________
_________________________________
```

