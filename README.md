# HireMe - Job Posting Platform

A comprehensive job posting platform built with Laravel where companies can post jobs and job seekers can apply with payment integration.

## 🚀 Features

- **Role-based Authentication** (Admin, Employee/Recruiter, Job Seeker)
- **JWT-based Authentication**
- **Job Management System**
- **Payment Integration** (Stripe/SSLCommerz)
- **File Upload** (CV/Resume - PDF, DOCX)
- **Admin Panel** with analytics
- **Application Management**

## 🛠️ Tech Stack

- **Language**: PHP
- **Framework**: Laravel
- **Database**: MySQL
- **Authentication**: JWT
- **Payment**: Stripe/SSLCommerz
- **File Storage**: Local/Cloud storage

## 📋 Prerequisites

- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & npm (for frontend assets)
- Git

## ⚡ Installation & Setup

### 1. Clone the Repository
- [https://github.com/Wrong1234/Job-Portal-Backend.git]
- cd Job-Portal-Backend

### 2. Install Dependencies
- composer install
### 3. Environment Configuration
 - cp .env.example .env
 - php artisan key:generate
### 4. Configure Environment Variables
 - Edit .env file with your database and payment credentials:
 - envDB_CONNECTION=mysql
 - DB_HOST=127.0.0.1
 - DB_PORT=3306
 - DB_DATABASE=job-portal
 - DB_USERNAME=root
 - DB_PASSWORD=""

 - JWT_SECRET=1oLj8K7ZKWq4TN4VxPH2kj1iXHRCxdslUivDz60Y9bRXlTzpkxNRJ8LhPNB2MQHA

### Stripe Configuration
 - STRIPE_SECRET=""
 - STRIPE_PUBLISHABLE=pk_test_51NcqnPGzVEVR5pGH0pMHML5q6PC3Iv5Q3aDClmJqVcXuSF1oHQ8Q47yO0kJFvGrROuc4e6PPH0WO80Ka3hhANT1p00KpWeuDFx
 - STRIPE_WEBHOOK_SECRET=whsec_092a7433c9460c4dc45780ad93f33c7edee0c904f20bf3d7a046bd64860f181d

### 5. Database Setup
 - php artisan migrate

### 7. Storage Setup
 - php artisan storage:link

### 8. Start the Server
 - php artisan serve
 - The application will be available at http://localhost:8000

👥 User Roles & Permissions
## Admin

 - Manage all users (create/update/delete)
 - Manage all jobs
 - View company analytics
 - View all applications

## Employee (Recruiter)

 - Post/edit/delete jobs for their company
 - View applicants for their jobs
 - Accept/reject applications
 - View applications to their jobs

## Job Seeker

 - View job listings
 - Apply for jobs (with CV upload + 100 Taka payment)
 - View application history
 - Cannot apply to same job twice

## API Authentication

  - Authorization: Bearer {your_jwt_token}

## API Endpoints
### Authentication
 - POST   /api/v1/register          # User registration
 - POST   /api/v1/login             # User login
 - POST   /api/v1/logout            # User logout
   
### User Management (Admin Only)
- GET    /api/v1/users            # Get all users
- POST   /api/v1/users            # Create new user
- PUT    /api/v1/users/{id}       # Update user
- DELETE /api/v1/users/{id}       # Delete user
  
### Job Management
 - GET    /api/v1/jobs                   # Get all jobs (public)
 - POST   /api/v1/jobs                   # Create job (Employee only)
 - GET    /api/v1/jobs/{id}              # Get specific job
 - PUT    /api/v1/jobs/{id}              # Update job (Employee - own jobs only)
 - DELETE /api/v1/jobs/{id}              # Delete job (Employee - own jobs only)

### Applications
 - GET    /api/v1/applications           # Get user's applications (Job Seeker)
 - POST   /api/jobs/{id}/apply           # Apply for job (Job Seeker)
 - GET    /api/v1/applications           # Get job applications (Employee and admin)
 - PUT    /{jobId}/{applicationId}       # Accept/reject application (Employee)

### Admin Analytics
 - GET    /api/v1/users/analytics        # Get platform analytics(Employee and Admin)
   
### Companies
 - GET    /api/v1/companies             # Get all companies
 - POST   /api/v1/applications          # Get all applications
 - PUT   /api/v1/applications/{id}      # Get all applications
 - GET   /api/v1/applications/{id}      # Get all applications
 - DELETE   /api/v1/applications/{id}   # Get all applications

### Payment
POST   /api/v1/jobs/payment         # Create payment intent
POST   api/v1/stripe/webhook'       # Confirm payment
GET    /api/v1/payments/success     # Get user invoices

### Payment Flow

 - Job Seeker selects a job to apply for
 - Upload CV/Resume (PDF or DOCX, max 50MB)
 - Payment Processing:

 - Create payment intent for 100 Taka
 - Redirect to Stripe/SSLCommerz payment page
 - Handle payment confirmation webhook


