# Gondwana Reservation Checker

A simple web tool to check reservation rates for Gondwana units.  
Includes a **frontend** (HTML, CSS, JS) and a **backend** (PHP API). Works in **GitHub Codespaces**, local IDEs, or any PHP-enabled environment.

---

## Features

- Check availability and rates for Gondwana units.
- Input unit name, arrival/departure dates, occupants, and ages.
- Display results dynamically with availability and rates.
- Responsive and easy-to-use interface.
- Works in Codespaces or any PHP-enabled environment.

---

## Prerequisites

- PHP 8.x
- Modern web browser (Chrome, Firefox, Edge, Safari)
- Git (to clone the repository)

---

## Getting Started

### 1. Clone the repository

```bash
gh repo clone tjamane/gondwana-reservation-checker
cd gondwana-reservation-checker
2. Start the PHP development server
bash
Copy code
php -S 0.0.0.0:8000 router.php
Frontend: http://localhost:8000/frontend/index.html

Backend API: http://localhost:8000/backend/api/rates.php

Tip: Keep the frontend/ and backend/ folders intact for routing to work.

Using the Project
Open frontend/index.html in a browser.

Fill in the form:

Unit: Ocean View Suite => -2147483637, Mountain Cabin => -2147483456

Arrival and Departure dates

Occupants and Ages (comma-separated)

Click Check Rates.

Results display dynamically:

Unit Name

Rate

Date Range

Availability status

Backend API
Endpoint: /backend/api/rates.php

Method: POST

Payload Example:

json
Copy code
{
  "Unit Name": "Ocean View Suite",
  "Arrival": "10/10/2025",
  "Departure": "12/10/2025",
  "Occupants": 2,
  "Ages": [25, 30]
}
Response Example:

json
Copy code
{
  "Legs": [
    {
      "Effective Average Daily Rate": 1500,
      "Error Code": 0
    }
  ]
}
Make sure the Unit Name matches the correct unique ID from the API.
