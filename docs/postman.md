# Event API (Postman)

This collection contains API endpoints for event management.

## Endpoints

- Login (authentication)
- Create/Update Event
- Get Events

**Note:** All event endpoints require authentication (active user session).

---

## 1. Login

**Method:** POST  
**URL:**  
http://localhost/reminder/public/?controller=auth&action=login

### Description
Authenticates user in the system.

This endpoint verifies user credentials and creates a session.

After successful login, a session (PHPSESSID cookie) is created and must be used for further requests.

### Headers
Content-Type: application/x-www-form-urlencoded

### Request Body (x-www-form-urlencoded)
username = ivan1919  
password = 1234

---

## 2. Add Event (Create/Update)

**Method:** POST  
**URL:**  
http://localhost/reminder/public/?controller=event&action=save

### Description
Creates a new event or updates an existing one.

Requires an authenticated user session.

### Headers
Content-Type: application/json

### Request Body (JSON)
{
  "title": "Project Meeting",
  "event_date": "2026-04-05 14:00:00",
  "description": "Discuss next sprint tasks",
  "reminder_time": "2026-04-05 13:30:00",
  "friend_ids": [2, 5]
}

---

## 3. Get Events

**Method:** GET  
**URL:**  
http://localhost/reminder/public/?controller=event&action=listJson

### Description
Returns all events of the authenticated user in JSON format.

This endpoint includes:
- events created by the user
- events shared with the user by friends

Each event contains additional fields:
- is_owner — indicates if the user is the owner of the event  
- added_by_friend_name — name of the friend who shared the event (if not owner)

### Headers
Accept: application/json

### Request Body
Not required.
