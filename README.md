# gondwana-reservation-checker

Absolutely! Here’s the full README content ready to copy and paste as README.md:
# Gondwana Reservation Checker

A simple web-based tool to check reservation rates for Gondwana units. This project includes a frontend (HTML, CSS, JS) and a backend (PHP API) for fetching rates. It is designed to run in **GitHub Codespaces**, local IDEs, or any environment with PHP installed.

---

## Features

- Check availability and rates for Gondwana units.
- Input unit name, arrival/departure dates, occupants, and ages.
- Display results dynamically with availability and rate.
- Responsive and easy-to-use interface.
- Fully compatible with Codespaces or any PHP-enabled IDE.

---

## Prerequisites

- PHP 8.x installed locally or available in your Codespace.
- Modern web browser (Chrome, Firefox, Edge, Safari).
- Git installed to clone the repository.

---

## Getting Started

### 1. Clone the repository

```bash
gh repo clone tjamane/gondwana-reservation-checker
cd gondwana-reservation-checker
2. Start the PHP development server
The project uses a router.php file to correctly route API requests:
php -S 0.0.0.0:8000 router.php
•	The frontend will be accessible at http://localhost:8000/frontend/index.html.
•	The backend API will be accessible at http://localhost:8000/backend/api/rates.php.
Keep the folder structure intact (frontend/ and backend/) for proper routing.
________________________________________
Using the Project
1.	Open frontend/index.html in a browser.
2.	Fill in the form:
o	Ocean View Suite => -2147483637 or Mountain Cabin => -,-2147483456
o	Arrival and Departure dates.
o	Occupants and Ages (comma-separated).
3.	Click Check Rates.
4.	Results will display dynamically:
o	Unit Name
o	Rate
o	Date Range
o	Availability status
________________________________________
Backend API
•	Endpoint: /backend/api/rates.php
•	Method: POST
•	Payload example:
{
  "Unit Name": "Ocean View Suite",
  "Arrival": "10/10/2025",
  "Departure": "12/10/2025",
  "Occupants": 2,
  "Ages": [25, 30]
}
•	Response example:
{
  "Legs": [
    {
      "Effective Average Daily Rate": 1500,
      "Error Code": 0
    }
  ]
}
 Ensure the Unit Name corresponds to the correct unique ID from the API.
________________________________________
Notes for Collaborators
•	Always keep the frontend and backend folders in their original locations.
•	Use Codespaces or a local PHP server with router.php for proper routing.
•	Check browser console for API requests and responses for debugging.
•	Commit changes before pushing or creating pull requests.
________________________________________
Contributing
1.	Fork the repository.
2.	Create a branch: git checkout -b feature/your-feature.
3.	Make changes and commit: git commit -m "Add new feature".
4.	Push to your branch: git push origin feature/your-feature.
5.	Open a pull request for review.

