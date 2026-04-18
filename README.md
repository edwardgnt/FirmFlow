# FirmFlow

Multi-tenant intake, follow-up, and workflow management app built with Laravel, Livewire, and PostgreSQL.

## Overview

FirmFlow is an operational workflow platform for managing inbound opportunities, client intake, follow-ups, and overdue work queues.

The project is designed around the needs of service-based organizations that need clear visibility into new inquiries, ownership, activity history, and follow-up execution. It combines intake tracking, operational dashboards, and action-oriented queue management into a single application.

This repository is actively in development.

## Features

Current functionality includes:

- Authentication and user access via Laravel starter kit
- Multi-tenant organization-scoped data model
- Dashboard with operational summary metrics
- Recent intake activity
- Needs-attention and overdue follow-up dashboard widgets
- Intake listing with filters
- Status badges for faster queue scanning
- Create intake workflow
- Intake detail view
- Follow-up history timeline
- Add follow-up workflow
- Edit intake status, urgency, and assignment
- Follow-up queue for overdue items
- Queue filtering by assigned user, intake status, and source

## Tech Stack

- Laravel
- Livewire
- PHP
- PostgreSQL
- Tailwind CSS
- Vite

## Project Goals

FirmFlow is being built as a production-style Laravel application that emphasizes:

- clear domain modeling
- organization-scoped data access
- operational workflows
- maintainable UI patterns
- practical SaaS-style feature development

## Screens Included in the Current Build

- Dashboard
- Intakes list
- Create intake
- Intake detail
- Follow-up queue

## Local Development

### Requirements

- PHP
- Composer
- Node.js / npm
- PostgreSQL
- Laravel Herd or another local Laravel environment

### Setup

```bash
git clone https://github.com/edwardgnt/FirmFlow.git
cd firmflow
composer install
npm install
cp .env.example .env
php artisan key:generate