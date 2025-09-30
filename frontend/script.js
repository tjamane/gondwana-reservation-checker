document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("bookingForm");
  const resultCard = document.getElementById("resultCard");
  const submitBtn = document.getElementById("submitBtn");

  // List of allowed unit names from backend (can expand if more units are added)
  const UNIT_NAMES = ["Ocean View Suite", "Mountain Cabin"];

  form.addEventListener("submit", async function(e) {
    e.preventDefault();

    // Clear previous results
    document.getElementById("resUnit").textContent = "";
    document.getElementById("resRate").textContent = "";
    document.getElementById("resDates").textContent = "";
    const availabilityEl = document.getElementById("resAvailability");
    availabilityEl.textContent = "";
    availabilityEl.className = "availability";
    resultCard.classList.remove("visible");

    submitBtn.disabled = true;
    submitBtn.textContent = "Checking...";

    let unit = document.getElementById("unitName").value.trim();
    const arrivalInput = document.getElementById("arrivalDate").value;
    const departureInput = document.getElementById("departureDate").value;
    const agesInput = document.getElementById("ages").value;

    if (!unit || !arrivalInput || !departureInput) {
      alert("Please fill in all required fields.");
      submitBtn.disabled = false;
      submitBtn.textContent = "Check Rates";
      return;
    }

    // Match unit name in a case-insensitive way
    const matchedUnit = UNIT_NAMES.find(u => u.toLowerCase() === unit.toLowerCase());
    if (!matchedUnit) {
      alert("Unknown Unit Name. Please use one of: " + UNIT_NAMES.join(", "));
      submitBtn.disabled = false;
      submitBtn.textContent = "Check Rates";
      return;
    }
    unit = matchedUnit; // normalize to proper case

    const ages = agesInput
      .split(",")
      .map(a => parseInt(a.trim()))
      .filter(a => !isNaN(a));
    const occupants = ages.length || 1;

    function formatDate(dateStr) {
      const [year, month, day] = dateStr.split("-");
      return `${day}/${month}/${year}`;
    }

    const payload = {
      "Unit Name": unit,
      "Arrival": formatDate(arrivalInput),
      "Departure": formatDate(departureInput),
      "Occupants": occupants,
      "Ages": ages
    };

    try {
      const res = await fetch("/api/rates.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (!res.ok) {
        alert(data.error || "Failed to fetch rates from backend");
        return;
      }

      const leg = data.Legs?.[0];
      let rateValue = leg ? leg["Effective Average Daily Rate"] : 0;
      const rateText = `N$${rateValue}`;
      let available = leg && leg["Error Code"] === 0;
      if (rateValue === 0) available = false;

      document.getElementById("resUnit").textContent = unit;
      document.getElementById("resRate").textContent = rateText;
      document.getElementById("resDates").textContent = `${payload.Arrival} → ${payload.Departure}`;
      availabilityEl.textContent = available ? "Available" : "Not Available";
      availabilityEl.className = available ? "availability available" : "availability unavailable";
      resultCard.classList.add("visible");

    } catch (err) {
      console.error(err);
      alert("An error occurred while fetching rates.");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Check Rates";
    }
  });
});
