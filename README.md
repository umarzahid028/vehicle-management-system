**Vehicle Reconditioning Tracking System – Developer Brief**

## Goal

Create a simple, effective web-based system to manage vehicle reconditioning from purchase to frontline-ready. The system should be lean, fast, and mobile-friendly for vendors and staff.

## 1. Vehicle Intake & Dispatch

-   System will pull new vehicle data from an FTP export file.
-   Notify the sales manager when new vehicles are added.
-   Sales manager logs in, assigns transporters, and records:
    -   Seller details (who the car was purchased from and where)
    -   Whether it’s arbitrable
-   Vendor and transporter tables with default transporter logic by location.
-   Attach release forms/gate passes per vehicle; transporters can access them via links.
-   VIN barcode scanning for quick vehicle lookup (active or archived).

## 2. Recon Workflow

### Inspection Stages (Triggered after Check-in):

-   **Performance Test Drive** – Transmission, engine, suspension, 4x4, steering.
-   **Arbitration Bucket** – If flagged issues are eligible.
-   **Diagnostic/Mechanical Repair** – Assign vendor, log cost, attach photos.
-   **Exterior Work** – PDR, paint, or touch-up.
-   **Interior Work** – Upholstery, radio, dash/steering wheel.
-   **Idle/Feature Check** – Lights, wipers, AC, horn, windows/locks, etc.
-   **Tires, Brakes, Fluids** – Separate lists; vendor-assigned or internal.

### Each Item Should Have:

-   Status: Pass / Repair / Replace
-   Optional photo
-   Vendor assignment or “in-house”
-   Time of assignment and completion
-   Internal-only or vendor-visible setting
-   Cost (if applicable)

**Custom Diagrams for exterior damage (CarMax-style).**

## 3. Post-Repair Steps

-   **Detail Bucket:** Detailer logs in and marks job complete.
-   **Sales Manager Walkaround:** Final QC check.
-   **Photos/Marketing Bucket:** Photos, buyer’s guide, stickers installed.
-   **Vehicle is moved to “Frontline Ready.”**

## 4. Post-Sale & Ongoing Maintenance

-   Vehicles are archived, not deleted.
-   **Reopen functionality** for we-owe or goodwill repairs.
-   **Track we-owe items** (tagged and assigned).
-   **Goodwill Repairs:**
    -   Task assignment, cost tracking
    -   Legal waiver signed digitally on a phone/tablet
    -   Generates signed PDF
    -   Sends PDF via SMS and attaches to vehicle profile

## 5. System Features

-   **Time-Based Alerts** with visual status (green/yellow/red).
-   **User Roles & Permissions.**
-   **Notifications** when vehicles enter user buckets.
-   **Custom Tags** (e.g., High Priority, Wholesale).
-   **Status History / Timeline View.**
-   **General Photo Gallery per Vehicle.**
-   **Ready-to-Post Checklist.**
-   **Bulk Actions** for faster workflow.
-   **VIN Barcode Scanning** for quick access.

## 6. Reporting & Logs

-   Weekly/monthly reporting:
    -   Repairs completed
    -   Cost per vendor or vehicle
    -   Open vehicles per stage
-   Basic exports or dashboards acceptable.
-   Full history/log retained per vehicle.

http://github.com/umarzahid028/trevinosauto/blob/033ea38c349696fa2a2a957b49122d7d3e2f83f4/routes/web.php
