# Leave Management System (Laravel + Filament)

## Project Overview

A complete Leave Management System built using Laravel and Filament to manage employees, holidays, leave requests, leave approvals, leave balances, annual leave allocation, and company-wide leave visibility.

The system should support two user roles:

* Admin
* Employee

---

# Technology Stack

## Backend

* Laravel 12

## Admin Panel

* Filament v4

## Database

* MySQL

## Authentication

* Filament Authentication

## Roles & Permissions

* Spatie Laravel Permission

## Notifications

* Filament Notifications

## Export Features

* Laravel Excel
* PDF Export

---

# User Roles

## Admin

Admin has full access to the system.

### Admin Permissions

* Manage Employees
* Manage Holidays
* Manage Weekend Settings
* Manage Leave Types
* Manage Leave Allocations
* Manage Leave Requests
* Approve Leave Requests
* Reject Leave Requests
* View Reports
* Export Reports
* View Employee Balances
* Configure Leave Policies

---

## Employee

Employee has limited access.

### Employee Permissions

* Login
* View Dashboard
* View Leave Balance
* Apply Leave
* View Leave History
* View Holidays
* View Leave Calendar
* View Employees On Leave

### Employee Restrictions

Employee cannot:

* Create Holidays
* Configure Weekends
* Approve Leave Requests
* View Other Employee Balances
* Modify Leave Policies

---

# Employee Management

Admin can:

* Create Employee
* Update Employee
* Activate Employee
* Deactivate Employee
* Assign Role

Employee Fields:

* Employee ID
* Name
* Email
* Phone
* Department
* Designation
* Joining Date
* Status

---

# Holiday Management

Admin can create company holidays.

Examples:

* Republic Day
* Independence Day
* Diwali
* Christmas
* New Year

Fields:

* Holiday Name
* Holiday Date
* Description
* Status

Employees can only view holidays.

---

# Weekend Configuration

Admin can define weekly off days.

Examples:

* Saturday & Sunday
* Sunday Only
* Friday & Saturday

Fields:

* Weekend Days
* Effective Date
* Status

Configured weekends must automatically be excluded from leave calculations.

---

# Leave Types

Default Leave Types:

* Casual Leave
* Sick Leave
* Earned Leave

Admin can create additional leave types.

Fields:

* Leave Type Name
* Maximum Allowed Days
* Status

---

# Annual Leave Allocation

## Default Leave Policy

Default annual leave:

24 Days Per Year

When a new employee is created:

* Leave balance should be created automatically.
* Default leave entitlement should be assigned automatically.

---

## Leave Allocation Settings

Admin can:

* Configure default annual leaves
* Allocate leaves individually
* Allocate leaves in bulk
* Credit extra leaves
* Debit leaves
* Adjust balances manually

---

## Individual Allocation

Admin can allocate leaves to a specific employee.

Fields:

* Employee
* Leave Type
* Allocated Leaves
* Year
* Remarks

---

## Bulk Allocation

Admin can allocate leaves to:

* Department
* Designation
* Employee Group

Actions:

* Allocate Default Leaves
* Allocate Custom Leaves

---

## Leave Adjustments

Admin can:

### Credit Leave

Example:

+2 Leaves

Reason:
Performance Reward

### Debit Leave

Example:

-1 Leave

Reason:
Correction

Every adjustment must be stored in history.

---

## Carry Forward Rules

Admin Settings:

* Allow Carry Forward
* Maximum Carry Forward Days

Example:

Remaining Leaves = 15

Carry Forward Limit = 10

Result:

10 Leaves Carried Forward
5 Leaves Expired

---

## Annual Leave Reset

System should support:

### Automatic Reset

Runs every year automatically.

### Manual Reset

Admin can trigger reset manually.

During reset:

* Total Leaves Updated
* Used Leaves Reset
* Remaining Leaves Recalculated
* Carry Forward Applied

---

# Leave Request Module

Employees can apply for leave.

Fields:

* Leave Type
* From Date
* To Date
* Reason
* Attachment (Optional)

Status:

* Pending
* Approved
* Rejected

Default Status:

Pending

---

# Leave Calculation Logic

System must calculate leave days automatically.

Rules:

* Holidays excluded
* Weekends excluded
* Approved leaves only deduct balance

Example:

Leave Period:

01 Jan – 10 Jan

Holiday:

05 Jan

Weekend:

Saturday & Sunday

Calculation:

Total Days = 10

Weekend Days = 2

Holiday Days = 1

Actual Leave Days = 7

Only 7 leaves should be deducted.

---

# Leave Approval Workflow

Employee
→ Submit Leave Request
→ Pending
→ Admin Review
→ Approve / Reject
→ Notification Sent

---

# Leave Balance Management

Each employee should have:

* Total Leaves
* Used Leaves
* Remaining Leaves

Example:

Total Leaves: 24

Used Leaves: 5

Remaining Leaves: 19

Employees can view only their own balance.

Admins can view all balances.

---

# Leave Calendar

Calendar should display:

* Employee Name
* Leave Dates
* Leave Type
* Status

Visible To:

* Admin
* Employee

Purpose:

Allow employees to know who is unavailable.

---

# Employees Currently On Leave

Dedicated widget/page showing:

* Employee Name
* Leave Type
* Leave Duration

Visible To:

* Admin
* Employee

---

# Dashboard

## Admin Dashboard

Cards:

* Total Employees
* Total Holidays
* Total Leave Requests
* Pending Requests
* Approved Requests
* Employees Currently On Leave

Charts:

* Monthly Leave Requests
* Leave Type Distribution
* Leave Trends

---

## Employee Dashboard

Cards:

* Remaining Leaves
* Used Leaves
* Pending Requests
* Upcoming Holidays

Widgets:

* My Leave History
* Upcoming Holidays
* Employees On Leave

---

# Notifications

## Admin Notifications

When:

New Leave Request Submitted

Notification:

New leave request submitted.

---

## Employee Notifications

When Leave Approved:

Your leave request has been approved.

When Leave Rejected:

Your leave request has been rejected.

---

# Reports

Admin Reports:

## Employee Leave Report

Filters:

* Employee
* Date Range

## Monthly Leave Report

Filters:

* Month
* Year

## Holiday Report

Filters:

* Year

## Leave Balance Report

Filters:

* Employee
* Year

---

# Export Features

Available Formats:

* Excel (.xlsx)
* PDF (.pdf)

All reports should support export.

---

# Filament Resources

Create resources for:

* Employees
* Holidays
* Weekend Settings
* Leave Types
* Leave Allocations
* Leave Balances
* Leave Requests
* Reports

---

# Scheduled Jobs

## Annual Leave Reset

Run Once Every Year

Tasks:

* Reset Balances
* Apply Carry Forward
* Allocate New Annual Leaves
* Create Audit Logs

---

# Audit Logs

Track all critical activities:

* Leave Allocation
* Leave Adjustment
* Leave Approval
* Leave Rejection
* Leave Reset
* Holiday Changes

Store:

* Action
* User
* Date & Time
* Remarks

---

# Future Enhancements

* Email Notifications
* WhatsApp Notifications
* Discord Notifications
* Attendance Management
* Check-In / Check-Out
* Payroll Integration
* AI Leave Analytics
* Leave Forecasting
* HR Insights Dashboard

---

# Development Phases

## Phase 1

Project Setup

* Laravel
* Filament
* Authentication
* Roles & Permissions

## Phase 2

Master Modules

* Employees
* Holidays
* Weekend Settings
* Leave Types

## Phase 3

Leave Management

* Leave Requests
* Approval Workflow
* Leave Balance Management

## Phase 4

Dashboard & Reports

* Charts
* Reports
* Exports

## Phase 5

Notifications & Automation

* Notifications
* Annual Reset
* Audit Logs

## Phase 6

Future Enhancements

* AI Features
* Attendance
* Payroll
