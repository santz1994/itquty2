# 📘 ITQuty Asset Management System - End User Guide

**Version:** 1.0  
**Last Updated:** October 16, 2025  
**For:** End Users, Admins, and Super Admins

---

## Table of Contents

1. [Getting Started](#getting-started)
2. [Dashboard Overview](#dashboard-overview)
3. [Ticket Management](#ticket-management)
4. [Asset Management](#asset-management)
5. [Asset Requests](#asset-requests)
6. [Daily Activities](#daily-activities)
7. [Reports & KPI Dashboard](#reports--kpi-dashboard)
8. [User Profile & Settings](#user-profile--settings)
9. [Notifications](#notifications)
10. [Search & Filters](#search--filters)
11. [FAQ](#faq)
12. [Troubleshooting](#troubleshooting)

---

## 1. Getting Started

### 1.1 Logging In

1. Open your web browser (Chrome, Firefox, Edge recommended)
2. Navigate to: `http://your-company-url.com`
3. Enter your **email address** and **password**
4. Click **"Login"** button
5. You'll be redirected to your dashboard

**First Time Login:**
- Use the credentials provided by your IT administrator
- You'll be prompted to change your password on first login

**Forgot Password:**
1. Click "Forgot Password?" link on login page
2. Enter your email address
3. Check your email for reset link
4. Click the link and set a new password

### 1.2 Understanding User Roles

ITQuty has 4 user roles with different access levels:

| Role | Access Level | What You Can Do |
|------|-------------|-----------------|
| **User** | Basic | Create tickets, view your assets, request assets |
| **Admin** | Advanced | Everything users can + manage tickets, assign assets, view audit logs |
| **Super Admin** | Full | Everything admins can + manage users, system settings, SLA policies |
| **Management** | View-Only | View-only access to reports, KPI dashboard, performance metrics |

---

## 2. Dashboard Overview

### 2.1 Home Dashboard

After logging in, you'll see your personalized dashboard:

**Widgets You'll See:**
- **📊 Statistics Cards:**
  - Total tickets assigned to you
  - Open tickets requiring action
  - Assets assigned to you
  - Pending asset requests

- **📋 Recent Tickets:**
  - Your most recent 5 tickets
  - Quick status view
  - Click to view details

- **🏷️ Recent Assets:**
  - Your assigned assets
  - Asset status indicators
  - Quick access to asset details

- **🔔 Notifications:**
  - Recent system notifications
  - Ticket updates
  - Asset request approvals

**Quick Actions:**
- ➕ **Create New Ticket** - Report an issue
- 📦 **Request Asset** - Request new equipment
- 🔍 **Search** - Find tickets or assets quickly

---

## 3. Ticket Management

### 3.1 Creating a New Ticket

**Step-by-Step:**

1. **Access Ticket Creation:**
   - Click **"Tickets"** in sidebar → **"Create Ticket"**
   - OR click **"Create New Ticket"** button on dashboard

2. **Fill in Required Information:**
   
   **Subject:** *(Required)*
   - Enter a clear, concise title
   - Example: "Printer not working on 3rd floor"

   **Description:** *(Required)*
   - Provide detailed information
   - Include error messages if any
   - Mention when the issue started

   **Ticket Type:** *(Required)*
   - Select from dropdown:
     - 🐛 Bug/Issue
     - 🆘 Support Request
     - 💡 Feature Request
     - 🔧 Maintenance
     - ❓ Question

   **Priority:** *(Required)*
   - 🔴 Critical - System down, business impact
   - 🟠 High - Important but not critical
   - 🟡 Medium - Normal priority
   - 🟢 Low - Can wait, minor issue

   **Location:** *(Required)*
   - Select your office location
   - Example: "Jakarta Office - 3rd Floor"

   **Asset:** *(Optional)*
   - If issue is related to specific equipment
   - Start typing asset name/tag to search

3. **Attach Files:** *(Optional)*
   - Click **"Upload Files"** button
   - Supported formats: Images, PDFs, Word docs
   - Max size: 10MB per file
   - Screenshots are helpful!

4. **Submit:**
   - Click **"Submit Ticket"** button
   - You'll receive a confirmation
   - Ticket number will be assigned (e.g., TKT-001234)

**✅ Success! Your ticket is created and assigned to the IT team.**

### 3.2 Viewing Your Tickets

**All Tickets Page:**
- Navigate to: **Tickets → All Tickets**
- You'll see a table with:
  - Ticket Code (e.g., TKT-001234)
  - Subject
  - Status (Open, In Progress, Resolved, Closed)
  - Priority
  - Assigned Admin
  - Created Date

**Filter Options:**
- 🔍 Search by ticket code or subject
- Filter by Status: Open, In Progress, Resolved, Closed
- Filter by Priority: Critical, High, Medium, Low
- Filter by Date Range

### 3.3 Ticket Status Meanings

| Status | Icon | What It Means |
|--------|------|---------------|
| **Open** | 🆕 | Ticket received, awaiting assignment |
| **In Progress** | ⏳ | Admin is working on your issue |
| **Waiting** | ⏸️ | Waiting for more information from you |
| **Resolved** | ✅ | Issue fixed, pending your confirmation |
| **Closed** | 🔒 | Ticket completed and closed |

### 3.4 Viewing Ticket Details

**Click on any ticket to see:**
- Full description
- All comments/updates
- Assigned admin
- Timeline of changes
- Attached files
- Related assets

**Actions You Can Take:**
- 💬 Add comment/update
- 📎 Attach additional files
- ✅ Confirm resolution (if status is "Resolved")
- 🔄 Request reopening (if closed but issue persists)

### 3.5 Adding Comments to Tickets

1. Open the ticket details page
2. Scroll to **"Add Comment"** section
3. Type your update or question
4. *(Optional)* Attach screenshots or files
5. Click **"Add Comment"**
6. The assigned admin will be notified

**Tips for Good Comments:**
- Be specific about what you tried
- Include error messages or screenshots
- Mention if the issue is urgent
- Update if situation changes

---

## 4. Asset Management

### 4.1 Viewing Your Assets

**Access Your Assets:**
- Navigate to: **Assets → My Assets**

**What You'll See:**
- List of all equipment assigned to you
- Asset details:
  - Asset Tag (e.g., AST-001234)
  - Asset Name (e.g., "Dell Laptop")
  - Model & Serial Number
  - Status (In Use, Available, Under Maintenance)
  - Assignment Date
  - Warranty Information

### 4.2 Asset QR Code Scanning

**Scan Asset QR Codes:**

1. Navigate to: **Assets → Scan QR Code**
2. Allow camera access when prompted
3. Point your device camera at the asset's QR code
4. Asset details will appear automatically
5. You can:
   - View full asset information
   - Report an issue with this asset
   - See maintenance history

**Alternatively:**
- Use your phone's QR scanner app
- It will open the asset detail page in your browser

### 4.3 Asset Status Indicators

| Status | Color | Meaning |
|--------|-------|---------|
| ✅ Available | Green | Ready to use |
| 🔵 In Use | Blue | Currently assigned |
| 🔧 Under Maintenance | Orange | Being serviced |
| 🔴 Retired | Red | No longer in service |
| ⚠️ Damaged | Yellow | Needs repair |

### 4.4 Reporting Asset Issues

**If you have a problem with your assigned asset:**

1. **Create a Ticket:**
   - Go to **Tickets → Create Ticket**
   - Select the problematic asset in "Asset" dropdown
   - Describe the issue clearly
   - Submit

2. **The ticket will be linked to the asset**
3. **IT team can track all issues related to that equipment**

---

## 5. Asset Requests

### 5.1 Requesting New Assets

**Need new equipment? Follow these steps:**

1. **Navigate to Asset Requests:**
   - Click **Asset Requests → New Request**

2. **Fill in Request Form:**

   **Asset Type:** *(Required)*
   - Select from dropdown:
     - 💻 Laptop/Computer
     - 🖥️ Monitor
     - ⌨️ Keyboard/Mouse
     - 🖨️ Printer
     - 📱 Mobile Device
     - 🔌 Accessories
     - 📞 Telephone
     - Other

   **Request Reason:** *(Required)*
   - Business Purpose
   - Replacement (damaged/old equipment)
   - New Hire
   - Project Requirements
   - Other

   **Description:** *(Required)*
   - Why you need this asset
   - Specifications if needed
   - Urgency level

   **Justification:**
   - Business impact
   - Productivity improvement
   - Current equipment issues

3. **Submit Request:**
   - Click **"Submit Request"**
   - Request sent to admin for approval

### 5.2 Tracking Your Requests

**View All Requests:**
- Navigate to: **Asset Requests → All Requests**

**Request Statuses:**
| Status | What It Means |
|--------|---------------|
| 🕐 Pending | Awaiting admin review |
| 👀 Under Review | Admin is evaluating |
| ✅ Approved | Request approved, asset being prepared |
| 📦 Fulfilled | Asset assigned to you |
| ❌ Rejected | Request denied (see reason in comments) |

**You'll Receive Notifications When:**
- Request is reviewed
- Request is approved/rejected
- Asset is ready for pickup
- Asset is assigned to you

### 5.3 After Approval

**Once your request is approved:**
1. You'll receive a notification
2. Admin will prepare the asset
3. You may be contacted to:
   - Pick up the asset
   - Verify specifications
   - Sign asset assignment form
4. Asset will appear in your "My Assets" list

---

## 6. Daily Activities

### 6.1 Viewing Activity Calendar

**For tracking IT team activities (Admin/Management):**

**Calendar View:**
- Navigate to: **Daily Activity → Calendar View**
- See activities by day/week/month
- Color-coded by ticket type
- Click on any day to see details

**List View:**
- Navigate to: **Daily Activity → Activity List**
- Filterable table of all activities
- Search by date, ticket, or admin

### 6.2 Activity Information

**Each activity shows:**
- Date & Time
- Admin/Technician name
- Ticket linked to activity
- Activity type (Installation, Repair, Maintenance, etc.)
- Location
- Duration
- Notes/Description

---

## 7. Reports & KPI Dashboard

### 7.1 KPI Dashboard (Management/Admin)

**Access:** **Reports → KPI Dashboard**

**Key Performance Indicators:**
- 📊 **Ticket Statistics:**
  - Total tickets this month
  - Average resolution time
  - Tickets by priority
  - Open vs Closed ratio

- 🏷️ **Asset Metrics:**
  - Total assets
  - Assets by status
  - Assets under maintenance
  - Warranty expiration alerts

- 👥 **User Activity:**
  - Active users
  - Tickets per user
  - Most requested asset types

- ⏱️ **Response Times:**
  - First response time
  - Average resolution time
  - SLA compliance rate

**Charts & Graphs:**
- Line charts: Ticket trends over time
- Pie charts: Asset distribution
- Bar charts: Ticket by priority/type
- Heat maps: Activity by day

### 7.2 Management Dashboard

**Access:** **Reports → Management Dashboard** *(Management role only)*

**Executive Summary:**
- High-level metrics
- Month-over-month comparisons
- Department performance
- Budget tracking (if configured)
- Critical issues requiring attention

### 7.3 Admin Performance Report

**Access:** **Reports → Admin Performance** *(Admin/Super Admin)*

**See Admin Metrics:**
- Tickets handled per admin
- Average resolution time per admin
- Customer satisfaction ratings
- Workload distribution
- Performance trends

---

## 8. User Profile & Settings

### 8.1 Viewing Your Profile

**Access Your Profile:**
1. Click your **name** in top-right corner
2. Select **"Profile"**

**Profile Information:**
- Name
- Email
- Role
- Department/Division
- Contact number
- Last login time

### 8.2 Changing Your Password

1. Go to **Profile**
2. Click **"Change Password"**
3. Enter:
   - Current Password
   - New Password
   - Confirm New Password
4. Click **"Update Password"**

**Password Requirements:**
- Minimum 8 characters
- At least one uppercase letter
- At least one number
- At least one special character (!@#$%^&*)

### 8.3 Notification Preferences

**Configure how you receive notifications:**
1. Go to **Profile → Settings**
2. Select notification channels:
   - 📧 Email notifications
   - 🔔 In-app notifications
3. Choose what to be notified about:
   - Ticket updates
   - Asset request status
   - System announcements
   - Maintenance schedules

---

## 9. Notifications

### 9.1 Viewing Notifications

**Notification Bell Icon** (top-right corner):
- Click to see recent notifications
- Red badge shows unread count
- Click "View All" for complete list

**Notification Types:**
| Icon | Type | When You Get It |
|------|------|-----------------|
| 🎫 | Ticket Update | Ticket status changed, comment added |
| 📦 | Asset Request | Request approved/rejected, asset assigned |
| 🏷️ | Asset Alert | Warranty expiring, maintenance due |
| 📢 | System | Announcements, scheduled maintenance |
| ✅ | Task Complete | Your action item resolved |

### 9.2 Managing Notifications

**Mark as Read:**
- Click on notification to mark as read
- Click "Mark All as Read" button

**Delete Notifications:**
- Hover over notification
- Click 🗑️ trash icon

**Filter Notifications:**
- All / Unread / By Type
- Search notifications by keyword

---

## 10. Search & Filters

### 10.1 Global Search

**Quick Search Bar** (top-right):
- Type ticket code (e.g., "TKT-123")
- Type asset tag (e.g., "AST-456")
- Type asset name
- Press Enter or click 🔍

**Results Show:**
- Tickets matching your search
- Assets matching your search
- Click to view details

### 10.2 Advanced Filtering

**On Tickets Page:**
- **Status Filter:** Open, In Progress, Resolved, Closed
- **Priority Filter:** Critical, High, Medium, Low
- **Date Range:** From-To dates
- **Assigned To:** Filter by admin
- **Ticket Type:** Bug, Support, Feature, etc.

**On Assets Page:**
- **Status Filter:** Available, In Use, Under Maintenance
- **Asset Type:** Laptop, Monitor, etc.
- **Location:** By office/floor
- **Assigned To:** Your assets / All assets
- **Warranty Status:** Active, Expired, Expiring Soon

### 10.3 Sorting

**Click column headers to sort:**
- ↑ Ascending order
- ↓ Descending order
- Click again to reverse

**Common Sorts:**
- Tickets: By date, priority, status
- Assets: By asset tag, name, status

### 10.4 Exporting Data

**Export to Excel:**
*(Admin/Super Admin only)*
1. Apply your filters
2. Click **"Export"** button
3. Choose format: Excel (.xlsx) or CSV
4. File downloads to your computer

---

## 11. FAQ

### Q1: I forgot my password. What do I do?
**A:** Click "Forgot Password?" on login page, enter your email, and follow the reset link sent to your inbox.

### Q2: How do I know if my ticket was received?
**A:** You'll see a success message after submitting, receive an email confirmation, and can see the ticket in "My Tickets."

### Q3: How long does it take to resolve a ticket?
**A:** Depends on priority:
- Critical: 2-4 hours
- High: 1 business day
- Medium: 2-3 business days
- Low: 5 business days
*(SLA times may vary by organization)*

### Q4: Can I have multiple assets assigned to me?
**A:** Yes! Users can have multiple assets (laptop, monitor, keyboard, etc.).

### Q5: What if I don't receive an expected asset?
**A:** Check your asset request status. Contact IT admin if status shows "Fulfilled" but you haven't received it.

### Q6: How do I report a damaged asset?
**A:** Create a new ticket, select the damaged asset from dropdown, and describe the issue with photos if possible.

### Q7: Can I transfer my asset to another user?
**A:** No. Only admins can reassign assets. Create a ticket requesting the transfer.

### Q8: The QR code won't scan. What should I do?
**A:** 
- Ensure good lighting
- Clean the QR code sticker
- Use the search function with asset tag instead
- Contact IT if sticker is damaged

### Q9: How do I check warranty status of my asset?
**A:** Go to **Assets → My Assets**, click on the asset, and view "Warranty Information" section.

### Q10: Can I edit a ticket after submitting?
**A:** You can add comments and attachments, but cannot edit the original ticket. Add a comment with updated information.

### Q11: What file types can I upload?
**A:** Images (.jpg, .png, .gif), Documents (.pdf, .doc, .docx), and Spreadsheets (.xls, .xlsx). Max 10MB per file.

### Q12: How do I escalate an urgent issue?
**A:** 
1. Create ticket with "Critical" priority
2. Add "[URGENT]" in subject
3. Call/message your IT admin directly
4. Add comment explaining urgency

### Q13: Why can't I see some menu options?
**A:** Menu visibility depends on your user role. Contact your admin if you need additional permissions.

### Q14: How do I request access to a system?
**A:** Create a ticket with subject "Access Request - [System Name]" and include justification.

### Q15: The system is slow. What should I do?
**A:** 
- Clear browser cache
- Try different browser
- Check internet connection
- Report via ticket if persistent

---

## 12. Troubleshooting

### Issue: Cannot Login

**Symptoms:** "Invalid credentials" message

**Solutions:**
1. ✅ Verify email address is correct
2. ✅ Check Caps Lock is off
3. ✅ Try "Forgot Password" reset
4. ✅ Clear browser cookies/cache
5. ✅ Try different browser
6. ✅ Contact IT admin if still failing

---

### Issue: Page Not Loading

**Symptoms:** White screen, loading spinner forever

**Solutions:**
1. ✅ Refresh page (F5)
2. ✅ Hard refresh (Ctrl+F5)
3. ✅ Clear browser cache
4. ✅ Check internet connection
5. ✅ Try incognito/private window
6. ✅ Report to IT if persists

---

### Issue: File Upload Failed

**Symptoms:** Error message when attaching files

**Solutions:**
1. ✅ Check file size (must be under 10MB)
2. ✅ Verify file type is allowed
3. ✅ Try renaming file (remove special characters)
4. ✅ Compress large images
5. ✅ Try uploading one file at a time
6. ✅ Use different browser

---

### Issue: Notifications Not Received

**Symptoms:** Missing email or app notifications

**Solutions:**
1. ✅ Check spam/junk folder
2. ✅ Verify notification settings in profile
3. ✅ Check email address is correct
4. ✅ Enable browser notifications
5. ✅ Contact IT to verify email server

---

### Issue: Cannot See My Assets

**Symptoms:** "My Assets" page is empty

**Solutions:**
1. ✅ Verify you have assets assigned (check with IT)
2. ✅ Clear any active filters
3. ✅ Check if viewing correct page
4. ✅ Try "All Assets" page and filter by your name
5. ✅ Contact IT admin to verify assignments

---

### Issue: Search Not Working

**Symptoms:** No results when searching

**Solutions:**
1. ✅ Check spelling
2. ✅ Try partial matches (e.g., "TKT-" instead of full code)
3. ✅ Remove filters that might be hiding results
4. ✅ Try searching in different sections
5. ✅ Clear search and try again

---

### Issue: QR Code Scanner Not Working

**Symptoms:** Camera doesn't open or won't scan

**Solutions:**
1. ✅ Allow camera permissions in browser
2. ✅ Try different browser (Chrome recommended)
3. ✅ Check camera is not used by another app
4. ✅ Clean QR code sticker
5. ✅ Ensure good lighting
6. ✅ Use manual asset tag search instead

---

## 📞 Getting Help

**If this guide doesn't solve your issue:**

1. **Create a Support Ticket:**
   - Go to **Tickets → Create Ticket**
   - Type: "Support Request"
   - Describe your problem with screenshots

2. **Contact IT Directly:**
   - Email: it-support@yourcompany.com
   - Phone: +XX XXX-XXX-XXXX
   - Office: IT Department, Floor X

3. **Emergency Support:**
   - For critical system-down issues
   - Call: +XX XXX-XXX-XXXX (24/7 hotline)

---

## 📝 Document Information

**Version:** 1.0  
**Last Updated:** October 16, 2025  
**Maintained By:** IT Department  
**Feedback:** Send suggestions to it-support@yourcompany.com

**Quick Reference Card:** See separate 1-page printable guide

---

**Thank you for using ITQuty Asset Management System! 🎉**

*This guide is subject to updates. Check the system for the latest version.*
