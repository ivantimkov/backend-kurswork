# System Specification Document (SSD)

## 1. System Overview
The system is a web-based application using MVC architecture for managing events and user interaction.

## 2. Architecture
Architecture pattern: MVC (Model-View-Controller)

- Model: handles data and database logic
- View: handles UI
- Controller: processes requests

## 3. Technology Stack
- Backend: PHP
- Frontend: HTML, CSS, JavaScript
- Database: MySQL

## 4. System Modules

### 4.1 Authentication Module
- User registration
- Login/logout
- Session handling

### 4.2 User Module
- Profile management
- Password update

### 4.3 Event Module
- Create event
- Update event
- Delete event
- Retrieve events

### 4.4 Social Module
- Friends system
- Chat system
- Forum

### 4.5 Admin Module
- User management
- Event moderation
- Statistics dashboard

## 5. Data Model

Entities:
- Users
- Events
- Messages
- Friends
- Forum posts

Relationships:
- User → Events (1:N)
- User → Messages (1:N)
- User ↔ Friends (M:N)

## 6. API Endpoints

### Get Events
- Method: GET
- URL: /?controller=event&action=listJson
- Description: Returns user events

### Save Event
- Method: POST
- URL: /?controller=event&action=save
- Description: Creates or updates event

## 7. Security
- Password hashing
- Session authentication
- Input validation

## 8. Error Handling
- HTTP status codes
- JSON error responses

## 9. Performance
- Optimized queries
- Efficient data handling

## 10. Scalability
- Modular architecture allows extension
