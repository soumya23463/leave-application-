# Discord Notification Integration - HRMS Leave Management System

## Overview

The HRMS Leave Management System should automatically send notifications to a Discord channel whenever important events occur.

Discord notifications will help HR teams, managers, and administrators stay informed in real time without opening the HRMS application.

---

# Objective

Integrate Discord Webhooks with the Laravel + Filament HRMS application.

The system should automatically send notifications for leave-related activities and employee management events.

---

# Technology Stack

* Laravel
* Filament
* MySQL
* Discord Webhook API
* Laravel Queue (Recommended)

---

# Discord Setup

## Create Discord Server

Create a dedicated server or use an existing company server.

Recommended Channels:

* #hr-notifications
* #leave-requests
* #system-alerts

---

## Create Webhook

Discord Channel Settings

→ Integrations

→ Webhooks

→ Create Webhook

Copy the generated Webhook URL.

Example:

DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/xxxxxxxxxxxxxxxx/yyyyyyyyyyyyyyyyyyyy

Store this URL securely in the application environment variables.

---

# Environment Configuration

.env

DISCORD_WEBHOOK_URL=

config/services.php

discord:

* webhook_url

---

# Notification Events

## 1. New Leave Request

Trigger:

Employee submits a leave request.

Notification:

Title:
New Leave Request

Details:

* Employee Name
* Leave Type
* From Date
* To Date
* Reason
* Status = Pending

Example:

📢 New Leave Request

Employee: Rahul Sharma

Leave Type: Casual Leave

From: 10 Jun 2026

To: 12 Jun 2026

Status: Pending Approval

---

## 2. Leave Approved

Trigger:

Admin approves leave request.

Notification:

✅ Leave Approved

Employee: Rahul Sharma

Leave Type: Casual Leave

Approved By: Admin

Approved Date: 10 Jun 2026

---

## 3. Leave Rejected

Trigger:

Admin rejects leave request.

Notification:

❌ Leave Rejected

Employee: Rahul Sharma

Leave Type: Casual Leave

Rejected By: Admin

Rejected Date: 10 Jun 2026

---

## 4. Holiday Created

Trigger:

Admin creates a company holiday.

Notification:

🎉 New Holiday Added

Holiday: Independence Day

Date: 15 Aug 2026

Description: National Holiday

---

## 5. Holiday Updated

Trigger:

Admin updates holiday details.

Notification:

📝 Holiday Updated

Holiday: Diwali

Updated By: Admin

---

## 6. Employee Created

Trigger:

New employee account created.

Notification:

👤 New Employee Added

Employee Name: Rahul Sharma

Joining Date: 01 Jun 2026

Role: Employee

---

## 7. Employee Status Changed

Trigger:

Employee activated or deactivated.

Notification:

⚠ Employee Status Changed

Employee: Rahul Sharma

Status: Inactive

Updated By: Admin

---

# Discord Message Format

Use Discord Embed Messages.

Embed Components:

* Title
* Description
* Fields
* Timestamp
* Color Indicator

Color Guidelines:

Yellow:
Pending Requests

Green:
Approved Requests

Red:
Rejected Requests

Blue:
Employee Activities

Purple:
Holiday Events

---

# Architecture

Recommended Flow

Filament Form

↓

Resource Action

↓

Model Observer

↓

Queue Job

↓

Discord Service

↓

Discord Webhook

↓

Discord Channel

---

# Observer Implementation

Observers should be used for:

* LeaveRequest
* Holiday
* User

Benefits:

* Centralized notification handling
* Cleaner code
* Automatic triggering

---

# Queue System

Recommended:

QUEUE_CONNECTION=database

Benefits:

* Faster user experience
* Retry failed notifications
* Better scalability

---

# Error Handling

If Discord API fails:

* Log error
* Continue application execution
* Do not block leave submission

Log Information:

* Event Type
* User ID
* Error Message
* Timestamp

---

# Future Enhancements

## Role Mentions

Automatically mention HR role.

Example:

@HR Team

New Leave Request Submitted

---

## Daily Summary

Daily Discord Report

Includes:

* Leave Requests
* Approved Leaves
* Rejected Leaves
* Employees on Leave

---

## Weekly Analytics

AI-generated summary:

* Leave Trends
* Department Statistics
* Upcoming Holidays

---

# Priority Implementation Order

Phase 1

* Discord Webhook Setup

Phase 2

* New Leave Request Notification

Phase 3

* Leave Approved Notification

Phase 4

* Leave Rejected Notification

Phase 5

* Queue Integration

Phase 6

* Holiday Notifications

Phase 7

* Employee Notifications

Phase 8

* Daily Summary Reports

Phase 9

* AI Leave Analytics

---

# Expected Result

The HRMS system should automatically notify Discord channels whenever important leave or employee events occur, providing real-time visibility to HR and management teams.
