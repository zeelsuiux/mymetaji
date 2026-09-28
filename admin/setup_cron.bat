@echo off
REM ============================================
REM MY METAJI - Windows Task Scheduler Setup
REM Sets up daily automatic payment reminders
REM ============================================

echo.
echo ========================================
echo   MY METAJI - Payment Reminder Setup
echo ========================================
echo.

REM Create the scheduled task to run daily at 9:00 AM
schtasks /create /tn "Metaji Payment Reminders" /tr "D:\xampp\php\php.exe \"D:\xampp\htdocs\projects\Mymetaji-Admin\cron_reminders.php\"" /sc daily /st 09:00 /f

if %errorlevel% equ 0 (
    echo.
    echo ✅ Task created successfully!
    echo    Task Name: Metaji Payment Reminders
    echo    Schedule: Daily at 9:00 AM
    echo    Script: cron_reminders.php
    echo.
    echo The task will automatically:
    echo    - Send WhatsApp reminders 2 days before payment due date
    echo    - Send overdue reminders after due date
    echo.
) else (
    echo.
    echo ❌ Failed to create task. Try running this file as Administrator.
    echo.
)

pause
