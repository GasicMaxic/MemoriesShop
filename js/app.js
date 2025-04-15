document.addEventListener("DOMContentLoaded", (event) => {
  const fileInput = document.getElementById("file");

  fileInput.addEventListener("change", () => {
    const brojSlika = fileInput.files.length;

    const lampaText = document.querySelector(
      "#order-summary .summary-item:nth-child(2) p"
    );
    const lampaPrice = document.querySelector(
      "#order-summary .summary-item:nth-child(2) span"
    );
    const dostavaPrice = document.querySelector(
      "#order-summary .summary-item:nth-child(3) span"
    );
    const totalPrice = document.querySelector(
      "#order-summary .summary-total span"
    );

    // Calculate prices
    const price = 1980;
    const lampaInt = brojSlika * price;
    const dostavaInt = 450; // Free delivery
    const ukupno = lampaInt + dostavaInt;

    // Update text and prices in the HTML
    lampaText.textContent = `${brojSlika}x Lampa`;
    lampaPrice.textContent = `${lampaInt} rsd`;
    totalPrice.textContent = `${ukupno} rsd`;
    dostavaPrice.textContent = `${dostavaInt} rsd`;
  });
});

document
  .getElementById("order-form")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this); // Collect form data
    const submitButton = document.querySelector("button[type='submit']");
    const spinner = document.getElementById("loading-spinner");

    // Disable the button and show the spinner
    submitButton.disabled = true;
    spinner.classList.remove("hidden");

    fetch("process.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Read response as text
      })
      .then((text) => {
        try {
          return JSON.parse(text); // Parse JSON response
        } catch (error) {
          console.error("Invalid JSON:", text);
          throw new Error("Server returned invalid JSON");
        }
      })
      .then((data) => {
        const popup = document.getElementById("popup");
        const popupMessage = document.getElementById("popup-message");

        if (data.status === "success") {
          popupMessage.textContent = data.message; // Set success message
        } else {
          popupMessage.textContent = data.message; // Set error message
        }

        popup.classList.remove("hidden"); // Show the popup
      })
      .catch((error) => {
        console.error("Error:", error);
        const popup = document.getElementById("popup");
        const popupMessage = document.getElementById("popup-message");

        popupMessage.textContent =
          "An unexpected error occurred. Please try again.";
        popup.classList.remove("hidden"); // Show the popup
      })
      .finally(() => {
        // Re-enable the button and hide the spinner
        submitButton.disabled = false;
        spinner.classList.add("hidden");
      });
  });

// Close the popup when the button is clicked
document.getElementById("close-popup").addEventListener("click", function () {
  const popup = document.getElementById("popup");
  popup.classList.add("hidden"); // Hide the popup
});
